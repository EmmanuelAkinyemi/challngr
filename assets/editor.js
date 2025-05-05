require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.34.1/min/vs' }});

require(['vs/editor/editor.main'], function() {
  // Create one editor per language
  const htmlEditor = monaco.editor.create(document.getElementById('htmlEditor'), {
    value: '<!-- HTML here -->\n',
    language: 'html',
    automaticLayout: true,
    minimap: { enabled: false }
  });

  const cssEditor = monaco.editor.create(document.getElementById('cssEditor'), {
    value: '/* CSS here */\n',
    language: 'css',
    automaticLayout: true,
    minimap: { enabled: false }
  });

  const jsEditor = monaco.editor.create(document.getElementById('jsEditor'), {
    value: '// JavaScript here\n',
    language: 'javascript',
    automaticLayout: true,
    minimap: { enabled: false }
  });

  const phpEditor = monaco.editor.create(document.getElementById('phpEditor'), {
    value: '<?php\n// PHP here\n',
    language: 'php',
    automaticLayout: true,
    minimap: { enabled: false }
  });

  // Wire up Run button
  document.getElementById('runBtn').addEventListener('click', () => {
    const html = htmlEditor.getValue();
    const css  = cssEditor.getValue();
    const js   = jsEditor.getValue();
    const php  = phpEditor.getValue();

    // Render HTML/CSS/JS in iframe
    document.getElementById('output').srcdoc = `
      <html>
        <head><style>${css}</style></head>
        <body>${html}
          <script>${js}<\/script>
        </body>
      </html>
    `;

    // Send PHP to server
    fetch('run-php.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code: php })
    })
    .then(r => r.text())
    .then(output => {
      document.getElementById('phpOutput').textContent = output;
    });
  });
});
