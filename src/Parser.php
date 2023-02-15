<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use League\CommonMark;
use League\CommonMark\Block\Element as Block;
use League\CommonMark\Inline\Element as Inline;
use atoum\apiblueprint\IntermediateRepresentation as IR;

class Parser
{
    const STATE_BEFORE_DOCUMENT = 0;
    const STATE_AFTER_DOCUMENT  = 1;
    const STATE_ROOT            = 2;

    const HEADER_GROUP    = 0;
    const HEADER_RESOURCE = 1;
    const HEADER_ACTION   = 2;
    const HEADER_UNKNOWN  = 3;

    const ACTION_REQUEST    = 0;
    const ACTION_RESPONSE   = 1;
    const ACTION_PARAMETERS = 2;
    const ACTION_ATTRIBUTES = 3;
    const ACTION_UNKNOWN    = 4;

    const PAYLOAD_BODY       = 0;
    const PAYLOAD_HEADERS    = 1;
    const PAYLOAD_ATTRIBUTES = 2;
    const PAYLOAD_SCHEMA     = 3;
    const PAYLOAD_UNKNOWN    = 4;

    protected static $_markdownParser = null;
    protected $_state                 = null;
    protected $_walker                = null;
    protected $_currentDocument       = null;
    protected $_currentNode           = null;
    protected $_currentIsEntering     = false;

    public function parse(string $apib): IR\Document
    {
        $this->_state             = self::STATE_BEFORE_DOCUMENT;
        $this->_currentDocument   = null;
        $this->_currentNode       = null;
        $this->_currentIsEntering = null;

        $markdownParser = static::getMarkdownParser();
        $this->_walker  = $markdownParser->parse($apib)->walker();

        //while ($event = $this->next()); exit;

        $this->parseStructure();

        return $this->_currentDocument;
    }

    protected function next(bool $expectEOF = false)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $event = $this->_walker->next();

        if (null === $event) {
            if (false === $expectEOF) {
                throw new Exception\ParserEOF('End of the document has been reached unexpectedly.');
            } else {
                return null;
            }
        }

        $this->debug(($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n");

        $this->_currentNode       = $event->getNode();
        $this->_currentIsEntering = $event->isEntering();

        return $event;
    }

    protected function peek()
    {
        $event = $this->_walker->next();

        if (null !== $event) {
            $this->debug('?? ' . ($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n");
        } else {
            $this->debug('?? null');
        }

        $this->debug('<< ' . ($this->_currentIsEntering ? 'Entering' : 'Leaving') . ' a ' . get_class($this->_currentNode) . ' node' . "\n");

        $this->_walker->resumeAt($this->_currentNode, $this->_currentIsEntering);
        $this->_walker->next(); // move to the state of the current node.

        return $event;
    }

    protected function parseStructure()
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        while ($event = $this->next(true)) {
            $node       = $event->getNode();
            $isEntering = $event->isEntering();

            $this->debug('%% state = ' . $this->_state . "\n");

            // End of the document.
            if (self::STATE_AFTER_DOCUMENT === $this->_state) {
                return;
            }
            // Beginning of the document.
            elseif (self::STATE_BEFORE_DOCUMENT === $this->_state &&
                $node instanceof Block\Document && $isEntering) {
                $this->parseDocument($node);
            }
            // Entering heading level 1: Either group section, resource
            // section, or a document description.
            elseif ($node instanceof Block\Heading && $isEntering &&
                    1 === $node->getLevel()) {
                $this->parseDescriptionOrGroupOrResource($node);
            }
        }
    }

    protected function parseDocument($node)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $this->_currentDocument = new IR\Document();

        $event = $this->peek();

        // The document is empty.
        if ($event->getNode() instanceof Block\Document && false === $event->isEntering()) {
            $this->_state = self::STATE_AFTER_DOCUMENT;

            return;
        }

        // The document might have metadata.
        if ($event->getNode() instanceof Block\Paragraph && true === $event->isEntering()) {
            $this->next();

            do {
                $event = $this->next();

                if ($event->getNode() instanceof Block\Paragraph && false === $event->isEntering()) {
                    break;
                }

                if ($event->getNode() instanceof Inline\Text &&
                    0 !== preg_match('/^([^:]+):(.*)$/', $event->getNode()->getContent(), $match)) {
                    $this->_currentDocument->metadata[mb_strtolower(trim($match[1]))] = trim($match[2]);
                }
            } while(true);
        }

        $this->_state = self::STATE_ROOT;

        return;
    }

    protected function parseDescriptionOrGroupOrResource(Block\Heading $node)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $headerContent = $this->getHeaderContent($node);

        switch ($this->getHeaderType($headerContent, $matches)) {
            case self::HEADER_GROUP:
                $group       = new IR\Group();
                $group->name = $matches[1];

                $this->_currentDocument->groups[] = $group;

                $level = $node->getLevel();

                while ($event = $this->peek()) {
                    $nextNode   = $event->getNode();
                    $isEntering = $event->isEntering();

                    if ($nextNode instanceof Block\Heading && $isEntering) {
                        if ($nextNode->getLevel() <= $level) {
                            return;
                        }

                        $this->next();

                        $nextHeaderContent = $this->getHeaderContent($nextNode);

                        if (self::HEADER_RESOURCE === $this->getHeaderType($nextHeaderContent, $nextMatches)) {
                            $this->parseResource(
                                $nextNode,
                                $group,
                                $nextMatches['name'] ?? '',
                                $nextMatches['uriTemplate'] ?? ''
                            );
                        }
                    } else {
                        $this->next();
                    }
                }

                break;

            case self::HEADER_RESOURCE:
                $this->parseResource(
                    $node,
                    $this->_currentDocument,
                    $matches['name'],
                    $matches['uriTemplate']
                );

                break;

            case self::HEADER_UNKNOWN:
                if (empty($this->_currentDocument->apiName)) {
                    $this->_currentDocument->apiName = $headerContent;
                }

                break;
        }
    }

    protected function parseResource(Block\Heading $node, $parent, string $name, string $uriTemplate)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $resource              = new IR\Resource();
        $resource->name        = trim($name);
        $resource->uriTemplate = strtolower(trim($uriTemplate));

        $parent->resources[] = $resource;

        $level = $node->getLevel();

        while ($event = $this->peek()) {
            $nextNode   = $event->getNode();
            $isEntering = $event->isEntering();

            if ($nextNode instanceof Block\Heading && $isEntering) {
                if ($nextNode->getLevel() <= $level) {
                    return;
                }

                $this->next();

                $nextHeaderContent = $this->getHeaderContent($nextNode);

                if (self::HEADER_ACTION === $this->getHeaderType($nextHeaderContent, $headerMatches)) {
                    $this->parseAction(
                        $nextNode,
                        $resource,
                        $headerMatches['name'] ?? '',
                        $headerMatches['requestMethod1'] ?? $headerMatches['requestMethod2'] ?? '',
                        $headerMatches['uriTemplate'] ?? ''
                    );
                }
            } else {
                $this->next();
            }
        }
    }

    protected function parseAction(
        Block\Heading $node,
        IR\Resource $resource,
        string $name,
        string $requestMethod,
        string $uriTemplate
    ) {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $action                = new IR\Action();
        $action->name          = trim($name);
        $action->requestMethod = strtoupper(trim($requestMethod));
        $action->uriTemplate   = strtolower(trim($uriTemplate));

        $resource->actions[] = $action;

        while ($event = $this->peek()) {
            $nextNode   = $event->getNode();
            $isEntering = $event->isEntering();

            if ($nextNode instanceof Block\Heading && $isEntering) {
                return;
            }

            $this->next();

            if ($nextNode instanceof Block\ListBlock && $isEntering) {
                while($event = $this->peek()) {
                    $nextNodeInListBlock = $event->getNode();
                    $isEntering          = $event->isEntering();

                    if ($nextNodeInListBlock instanceof Block\ListBlock && !$isEntering) {
                        return;
                    }

                    $this->next();

                    if ($nextNodeInListBlock instanceof Block\ListItem && $isEntering) {
                        $event = $this->peek();

                        $nextNodeInListItem = $event->getNode();
                        $isEntering         = $event->isEntering();

                        if ($nextNodeInListItem instanceof Block\Paragraph && $isEntering) {
                            $this->next();

                            // Move to the end of the paragraph.
                            while(
                                (($event = $this->next()) ?? false) &&
                                !($event->getNode() instanceof Block\Paragraph && false === $event->isEntering())
                            );

                            $actionContent = trim($nextNodeInListItem->getStringContent());
                            $actionType    = $this->getActionType($actionContent, $actionMatches);

                            if (self::ACTION_REQUEST === $actionType ||
                                self::ACTION_RESPONSE === $actionType) {
                                $requestOrResponse = null;

                                if (self::ACTION_REQUEST === $actionType) {
                                    $request            = new IR\Request();
                                    $request->name      = trim($actionMatches['name'] ?? '');
                                    $request->mediaType = trim($actionMatches['mediaType'] ?? '');

                                    $action->messages[] = $request;

                                    $requestOrResponse = $request;
                                } else {
                                    $response            = new IR\Response();
                                    $response->mediaType = trim($actionMatches['mediaType'] ?? '');

                                    if (!isset($actionMatches['statusCode']) || empty($actionMatches['statusCode'])) {
                                        $response->statusCode = 200;
                                    } else {
                                        $response->statusCode = intval($actionMatches['statusCode']);
                                    }

                                    $action->messages[] = $response;

                                    $requestOrResponse = $response;
                                }

                                $event             = $this->peek();
                                $payloadNode       = $event->getNode();
                                $payloadIsEntering = $event->isEntering();

                                // Assume the paragraph is the description.
                                if ($payloadNode instanceof Block\Paragraph && $payloadIsEntering) {
                                    $this->next();
                                    $requestOrResponse->description = $payloadNode->getStringContent();

                                    // But maybe it is wrong. So let's
                                    // peek the next node and see what it
                                    // is.
                                    $event             = $this->peek();
                                    $payloadNode       = $event->getNode();
                                    $payloadIsEntering = $event->isEntering();
                                }

                                // There is a list of payloads.
                                if ($payloadNode instanceof Block\ListBlock && $payloadIsEntering) {
                                    $this->next();
                                    $this->parsePayload($payloadNode, $requestOrResponse);
                                }

                                // If there is a description and no
                                // payload, then the description is the
                                // `Body` payload.
                                if (!empty($requestOrResponse->description) && null === $requestOrResponse->payload) {
                                    $payload                        = new IR\Payload();
                                    $payload->body                  = $requestOrResponse->description;
                                    $requestOrResponse->description = '';
                                    $requestOrResponse->payload     = $payload;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function parsePayload($node, IR\Message $requestOrResponse)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        // The payload is a paragraph representing the body.
        if ($node instanceof Block\Paragraph) {
            $payload = new IR\Payload();
            $payload->body = $node->getStringContent();

            $requestOrResponse->payload = $payload;

            // Move to the end of the paragraph.
            while(
                (($event = $this->next()) ?? false) &&
                !($event->getNode() instanceof Block\Paragraph && false === $event->isEntering())
            );
        } elseif ($node instanceof Block\ListBlock) {
            $payload                    = new IR\Payload();
            $requestOrResponse->payload = $payload;

            while($event = $this->next()) {
                $nextNodeInListBlock = $event->getNode();
                $isEntering          = $event->isEntering();

                if ($nextNodeInListBlock instanceof Block\ListBlock && !$isEntering) {
                    return;
                }

                if ($nextNodeInListBlock instanceof Block\ListItem && $isEntering) {
                    $event = $this->peek();

                    $nextNodeInListItem = $event->getNode();
                    $isEntering         = $event->isEntering();

                    if ($nextNodeInListItem instanceof Block\Paragraph && $isEntering) {
                        $this->next();

                        // Move to the end of the paragraph.
                        while(
                            (($event = $this->next()) ?? false) &&
                            !($event->getNode() instanceof Block\Paragraph && false === $event->isEntering())
                        );

                        $payloadContent = trim($nextNodeInListItem->getStringContent());
                        $payloadType    = $this->getPayloadType($payloadContent, $payloadMatches);

                        switch ($payloadType) {
                            case self::PAYLOAD_BODY:
                            case self::PAYLOAD_SCHEMA:
                                $event          = $this->peek();
                                $bodyOrSchemaNode       = $event->getNode();
                                $bodyOrSchemaIsEntering = $event->isEntering();

                                if (($bodyOrSchemaNode instanceof Block\Paragraph && $bodyOrSchemaIsEntering) ||
                                    ($bodyOrSchemaNode instanceof Block\FencedCode && $bodyOrSchemaIsEntering)) {
                                    $this->next();

                                    $bodyOrSchemaContent = $bodyOrSchemaNode->getStringContent();

                                    if (self::PAYLOAD_BODY === $payloadType) {
                                        $payload->body = $bodyOrSchemaContent;
                                    } else {
                                        $payload->schema = $bodyOrSchemaContent;
                                    }
                                }

                                break;

                            case self::PAYLOAD_HEADERS:
                                $event             = $this->peek();
                                $headersNode       = $event->getNode();
                                $headersIsEntering = $event->isEntering();

                                if ($headersNode instanceof Block\Paragraph && $headersIsEntering) {
                                    $this->next();

                                    $payload->headers = array_merge(
                                        $payload->headers,
                                        Http\Response::parseRawHeaders($headersNode->getStringContent())
                                    );
                                }

                                break;

                            case self::PAYLOAD_ATTRIBUTES:
                                throw new \RuntimeException('Payload Attributes are not implemented yet.');

                                break;
                        }
                    }
                }
            }
        }
    }

    public function getHeaderType(string $headerContent, &$matches = []): int
    {
        // Resource group section.
        if (0 !== preg_match('/^Group\h+([^\[\]\(\)]+)/', $headerContent, $matches)) {
            return self::HEADER_GROUP;
        }

        // Resource section.
        if (0 !== preg_match('/^(?<name>[^\[]+)\[(?<uriTemplate>\/[^\]]+)\]/', $headerContent, $matches)) {
            return self::HEADER_RESOURCE;
        }

        // Action section.
        if (0 !== preg_match('/(?:^(?<requestMethod1>[A-Z]+)$)|(?:^(?<name>[^\[]+)\[(?<requestMethod2>[A-Z]+)(?:\h+(?<uriTemplate>\/[^\]]+))?\])/', $headerContent, $matches)) {
            if (empty($matches['requestMethod1'])) {
                $matches['requestMethod1'] = null;
            }

            return self::HEADER_ACTION;
        }

        // API name.
        return self::HEADER_UNKNOWN;
    }

    public function getHeaderContent(Block\Heading $node): string
    {
        return trim($node->getStringContent() ?? '');
    }

    public function getActionType(string $actionContent, &$matches = []): int
    {
        // Request.
        if (0 !== preg_match('/^Request(?<name>\h+[^\(]*)?(?:\((?<mediaType>[^\)]+)\))?/', $actionContent, $matches)) {
            return self::ACTION_REQUEST;
        }

        // Response.
        if (0 !== preg_match('/^Response(?<statusCode>\h+\d+)?(?:\h+\((?<mediaType>[^\)]+)\))?/', $actionContent, $matches)) {
            return self::ACTION_RESPONSE;
        }

        // Unknown.
        return self::ACTION_UNKNOWN;
    }

    protected function getPayloadType(string $payloadContent, &$matches = []): int
    {
        // Body.
        if (0 !== preg_match('/^Body/', $payloadContent, $matches)) {
            return self::PAYLOAD_BODY;
        }

        // Headers.
        if (0 !== preg_match('/^Headers/', $payloadContent, $matches)) {
            return self::PAYLOAD_HEADERS;
        }

        // Attributes.
        if (0 !== preg_match('/^Attributes(?:\h+\((?<typeDefinition>[^\)]+)\))?/', $payloadContent, $matches)) {
            return self::PAYLOAD_ATTRIBUTES;
        }

        // Schema.
        if (0 !== preg_match('/^Schema/', $payloadContent, $matches)) {
            return self::PAYLOAD_SCHEMA;
        }

        // Unknown.
        return self::PAYLOAD_UNKNOWN;
    }

    protected function getMarkdownParser()
    {
        if (null === static::$_markdownParser) {
            static::$_markdownParser = new CommonMark\DocParser(
                CommonMark\Environment::createCommonMarkEnvironment()
            );
        }

        return static::$_markdownParser;
    }

    private function debug(string $message)
    {
        //echo $message;
    }
}
