<!doctype html>

<html lang="en">
  <head>
    <title>Documentation for an HTTP API</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-type" content="text/javascript; charset=utf-8" />
    <meta http-equiv="content-type" content="text/css; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="generator" content="atoum-apiblueprint-extension (https://github.com/Hywan/atoum-apiblueprint-extension)" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <style>
      :root {
        --background-light: #fff;
        --background-semi-light: #fafcfc;
        --background-dark: #2d3134;
        --column-1-width: 14%;
        --column-2-width: 50%;
        --column-3-width: 50%;
        --gutter: 2rem;
      }

      * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }

      body {
        color: #2c3135;
        font-family: Open Sans, Helvetica, Arial, sans-serif;
        margin: 0;
        padding: 0;
        scroll-behavior: smooth;
      }

      header {
        padding: 1rem;
        background: var(--background-light);
      }

      main {
        display: flex;
        flex-direction: row;
      }

      nav {
        position: relative;
        width: var(--column-1-width);
        padding: var(--gutter) calc(var(--gutter) / 2) var(--gutter) calc(var(--gutter) / 2 - .5rem);
        font-size: .9rem;
        border-right: 1px #f0f4f7 solid;
        background-color: var(--background-semi-light);
      }

      nav > div {
        position: sticky;
        top: var(--gutter);
      }

      nav ol {
        list-style: none;
        margin: 0;
        padding: 0 0 0 .5rem;
      }

      nav > div > ol > li ~ li {
        margin-top: var(--gutter);
      }

      nav li {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }

      article {
        flex: 1;
        background-image: linear-gradient(
          to right,
          var(--background-light) var(--column-2-width),
          var(--background-dark) var(--column-3-width)
        );
      }

      section {
        padding: calc(var(--gutter) / 2);
        overflow: hidden;
      }

      section + section {
        margin-top: var(--gutter);
      }

      section {
        display: grid;
        grid-template-columns: var(--column-2-width) var(--column-3-width);
        grid-column-gap: var(--gutter);
      }

      section > * {
        grid-column: 1;
      }

      section > .heading--type-group {
        grid-column: 1 / 3;
        display: grid;
        grid-template-columns: calc(var(--column-2-width) - 1rem) calc(var(--column-3-width) - 1rem);
        grid-column-gap: var(--gutter);
      }

      section > .heading--type-group > *,
      section > .heading--type-resource > * {
        grid-column: 1;
      }

      section > .heading--type-group > .heading--type-resource,
      section > .heading--type-resource {
        grid-column: 1 / 3;
        display: grid;
        grid-template-columns: var(--column-2-width) var(--column-3-width);
      }

      section > .heading--type-group > .heading--type-resource > *,
      section > .heading--type-resource > * {
        grid-column: 1;
      }

      section > .heading--type-group > .heading--type-resource > .heading--type-action,
      section > .heading--type-resource > .heading--type-action {
        grid-column: 2;
        margin-right: var(--gutter);
        color: #dde4e8;
      }

      section > div:first-of-type {
        margin-bottom: calc(var(--gutter) * 2);
      }

      section > div ~ div {
        margin: calc(var(--gutter) * 2) 0;
      }

      section > div ~ div > h1 {
        position: relative;
      }

      section > div ~ div > h1::after {
        position: absolute;
        content: '';
        top: calc(var(--gutter) * -2.5 - 3px);
        right: calc(-100% + 1rem);
        left: -1rem;
        height: 3px;
        background-image: linear-gradient(
          to right,
          #f0f4f7 calc(var(--column-2-width) + 1rem),
          #33383b calc(var(--column-3-width) + 1rem)
        );
      }

      section > div.heading--type-resource > h1::after {
        background-image: linear-gradient(
          to right,
          #f0f4f7 calc(var(--column-2-width)),
          #33383b calc(var(--column-3-width))
        );
      }

      h1, h2, h3, h4, h5, h6 {
        font-weight: normal;
      }

      a {
        color: #0099e5;
        text-decoration: none;
      }

      a:hover, a:focus {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding: 0 0 0 1.5rem;
      }

      img {
        vertical-align: middle;
      }

      pre, code {
        font-family: Source Code Pro, Menlo, monospace;
        font-size: .9rem;
      }

      pre {
        width: auto;
        margin: 0;
        padding: 1.5rem 2rem;
      }

      .heading--type-action code {
        color: #d0d0d0;
      }

      .heading--type-action pre {
        background: #272b2d;
      }

      .metadata {
        display: none;
      }
    </style>
  </head>
  <body>
    <main>
      <nav>
        <div>
          <span style="display: block; margin: 0 0 var(--gutter) 1rem">
            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjUxcHgiIGhlaWdodD0iNDdweCIgdmlld0JveD0iMCAwIDUxIDQ3IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+Cjx0aXRsZT5hcGktYmx1ZXByaW50PC90aXRsZT4KPGRlc2M+QVBJIEJsdWVwcmludCBsb2dvIChvcHRpbWlzZWQpPC9kZXNjPgo8ZGVmcz48L2RlZnM+CjxnIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgo8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNDQ1LjAwMDAwMCwgLTEzNy4wMDAwMDApIj4KPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNDQ1LjAwMDAwMCwgMTM2LjAwMDAwMCkiPgo8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjAwMDAwMCwgMS4wMDAwMDApIj4KPHBhdGggZD0iTTI5LjIxNTM2MDMsMTYuNjMyODY0NyBMMzcuODQ2MTI5NSwzMC4yNTg5MTUxIEwzOC40Nzg5MjA5LDMxLjI1Nzk1MSBMNDAuNDYzNTg1LDI5Ljk4MzgxODMgTDM5LjgzMDc5MzYsMjguOTg0NzgyNCBMMzEuMjAwMDI0NCwxNS4zNTg3MzIgTDMwLjU2NzIzMjksMTQuMzU5Njk2MSBMMjguNTgyNTY4OCwxNS42MzM4Mjg4IEwyOS4yMTUzNjAzLDE2LjYzMjg2NDcgTDI5LjIxNTM2MDMsMTYuNjMyODY0NyBMMjkuMjE1MzYwMywxNi42MzI4NjQ3IFogTTE5Ljc5OTk3NTYsMTUuMzU4NzMyIEwxMS4xNjkyMDY0LDI4Ljk4NDc4MjQgTDEwLjUzNjQxNSwyOS45ODM4MTgzIEwxMi41MjEwNzkxLDMxLjI1Nzk1MSBMMTMuMTUzODcwNSwzMC4yNTg5MTUxIEwyMS43ODQ2Mzk3LDE2LjYzMjg2NDcgTDIyLjQxNzQzMTIsMTUuNjMzODI4OCBMMjAuNDMyNzY3MSwxNC4zNTk2OTYxIEwxOS43OTk5NzU2LDE1LjM1ODczMiBMMTkuNzk5OTc1NiwxNS4zNTg3MzIgTDE5Ljc5OTk3NTYsMTUuMzU4NzMyIFoiIGZpbGw9IiM1RTlDRkYiPjwvcGF0aD4KPHBhdGggZD0iTTI1LjUsMTguMTY4MDY3MiBDMzAuNDgzMzA3OCwxOC4xNjgwNjcyIDM0LjUyMzA3NjksMTQuMTAxMDA2OCAzNC41MjMwNzY5LDkuMDg0MDMzNjEgQzM0LjUyMzA3NjksNC4wNjcwNjAzOCAzMC40ODMzMDc4LDAgMjUuNSwwIEMyMC41MTY2OTIyLDAgMTYuNDc2OTIzMSw0LjA2NzA2MDM4IDE2LjQ3NjkyMzEsOS4wODQwMzM2MSBDMTYuNDc2OTIzMSwxNC4xMDEwMDY4IDIwLjUxNjY5MjIsMTguMTY4MDY3MiAyNS41LDE4LjE2ODA2NzIgWiBNMjUuNSwxNS43OTgzMTkzIEMyMS44MTY2ODU2LDE1Ljc5ODMxOTMgMTguODMwNzY5MiwxMi43OTIyMzEyIDE4LjgzMDc2OTIsOS4wODQwMzM2MSBDMTguODMwNzY5Miw1LjM3NTgzNjAxIDIxLjgxNjY4NTYsMi4zNjk3NDc5IDI1LjUsMi4zNjk3NDc5IEMyOS4xODMzMTQ0LDIuMzY5NzQ3OSAzMi4xNjkyMzA4LDUuMzc1ODM2MDEgMzIuMTY5MjMwOCw5LjA4NDAzMzYxIEMzMi4xNjkyMzA4LDEyLjc5MjIzMTIgMjkuMTgzMzE0NCwxNS43OTgzMTkzIDI1LjUsMTUuNzk4MzE5MyBaIiBmaWxsPSIjNUU5Q0ZGIj48L3BhdGg+CjxlbGxpcHNlIGZpbGw9IiNGRkZGRkYiIGN4PSI5IiBjeT0iMzgiIHJ4PSIzIiByeT0iMyI+PC9lbGxpcHNlPgo8cGF0aCBkPSJNNDEuOTc2OTIzMSw0NyBDNDYuOTYwMjMwOSw0NyA1MSw0Mi45MzI5Mzk2IDUxLDM3LjkxNTk2NjQgQzUxLDMyLjg5ODk5MzIgNDYuOTYwMjMwOSwyOC44MzE5MzI4IDQxLjk3NjkyMzEsMjguODMxOTMyOCBDMzYuOTkzNjE1MywyOC44MzE5MzI4IDMyLjk1Mzg0NjIsMzIuODk4OTkzMiAzMi45NTM4NDYyLDM3LjkxNTk2NjQgQzMyLjk1Mzg0NjIsNDIuOTMyOTM5NiAzNi45OTM2MTUzLDQ3IDQxLjk3NjkyMzEsNDcgWiBNNDEuOTc2OTIzMSw0NC42MzAyNTIxIEMzOC4yOTM2MDg2LDQ0LjYzMDI1MjEgMzUuMzA3NjkyMyw0MS42MjQxNjQgMzUuMzA3NjkyMywzNy45MTU5NjY0IEMzNS4zMDc2OTIzLDM0LjIwNzc2ODggMzguMjkzNjA4NiwzMS4yMDE2ODA3IDQxLjk3NjkyMzEsMzEuMjAxNjgwNyBDNDUuNjYwMjM3NSwzMS4yMDE2ODA3IDQ4LjY0NjE1MzgsMzQuMjA3NzY4OCA0OC42NDYxNTM4LDM3LjkxNTk2NjQgQzQ4LjY0NjE1MzgsNDEuNjI0MTY0IDQ1LjY2MDIzNzUsNDQuNjMwMjUyMSA0MS45NzY5MjMxLDQ0LjYzMDI1MjEgWiIgZmlsbD0iIzVFOUNGRiI+PC9wYXRoPgo8cGF0aCBkPSJNOS4wMjMwNzY5Miw0NyBDMTQuMDA2Mzg0Nyw0NyAxOC4wNDYxNTM4LDQyLjkzMjkzOTYgMTguMDQ2MTUzOCwzNy45MTU5NjY0IEMxOC4wNDYxNTM4LDMyLjg5ODk5MzIgMTQuMDA2Mzg0NywyOC44MzE5MzI4IDkuMDIzMDc2OTIsMjguODMxOTMyOCBDNC4wMzk3NjkxNCwyOC44MzE5MzI4IDAsMzIuODk4OTkzMiAwLDM3LjkxNTk2NjQgQzAsNDIuOTMyOTM5NiA0LjAzOTc2OTE0LDQ3IDkuMDIzMDc2OTIsNDcgWiBNOS4wMjMwNzY5Miw0NC42MzAyNTIxIEM1LjMzOTc2MjQ4LDQ0LjYzMDI1MjEgMi4zNTM4NDYxNSw0MS42MjQxNjQgMi4zNTM4NDYxNSwzNy45MTU5NjY0IEMyLjM1Mzg0NjE1LDM0LjIwNzc2ODggNS4zMzk3NjI0OCwzMS4yMDE2ODA3IDkuMDIzMDc2OTIsMzEuMjAxNjgwNyBDMTIuNzA2MzkxNCwzMS4yMDE2ODA3IDE1LjY5MjMwNzcsMzQuMjA3NzY4OCAxNS42OTIzMDc3LDM3LjkxNTk2NjQgQzE1LjY5MjMwNzcsNDEuNjI0MTY0IDEyLjcwNjM5MTQsNDQuNjMwMjUyMSA5LjAyMzA3NjkyLDQ0LjYzMDI1MjEgWiIgZmlsbD0iIzVFOUNGRiI+PC9wYXRoPgo8L2c+CjwvZz4KPC9nPgo8L2c+Cjwvc3ZnPgo=" height="40px" />
            <strong style="margin-left: .5rem">HTTP API</strong>
          </span>
          <ol id="toc" />
        </div>
      </nav>

      <article>
        <header>
          <h1><?php echo $title; ?></h1>
        </header>

<?php

foreach ($body as $file) {
    echo $file;
}

?>
      </article>
    </main>

    <script>
      // Generate table of contents.
      (function () {
        var link = function (href, text) {
          var out = document.createElement('a');
          out.setAttribute('href', href);
          out.appendChild(document.createTextNode(text));

          return out;
        };
        var list     = document.getElementById('toc');
        var sections = document.querySelectorAll('section');

        for (var i = 0; i < sections.length; ++i) {
          var section = sections.item(i);

          var sectionList = document.createElement('ol');
          var handle      = document.createElement('li');

          handle.appendChild(sectionList);
          list.appendChild(handle);

          var h1s = section.querySelectorAll('h1');

          for (var j = 0; j < h1s.length; ++j) {
            var h1 = h1s.item(j);

            var h1ListItem = document.createElement('li');
            var h1List     = document.createElement('ol');

            h1ListItem.appendChild(link('#' + h1.getAttribute('id'), h1.textContent));
            h1ListItem.appendChild(h1List);
            sectionList.appendChild(h1ListItem);

            var h2s = h1.parentNode.querySelectorAll('h2');

            for (var k = 0; k < h2s.length; ++k) {
              var h2 = h2s.item(k);

              var h2ListItem = document.createElement('li');

              h2ListItem.appendChild(link('#' + h2.getAttribute('id'), h2.textContent));
              h1List.appendChild(h2ListItem);
            }
          }
        }
      })();
    </script>
  </body>
</html>
