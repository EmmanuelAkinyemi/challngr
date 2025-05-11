// editor.js - Enhanced Monaco Editor Integration

// Configure Monaco loader
require.config({ 
  paths: { 
      'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.36.1/min/vs'
  }
});

// Initialize editors when Monaco is loaded
require(['vs/editor/editor.main'], function() {
  // Store editor instances
  const editors = {};
  const editorContainers = ['html', 'css', 'js', 'php'];
  
  // Initialize all editors
  editorContainers.forEach(lang => {
      const container = document.getElementById(`${lang}Editor`);
      const defaultValue = container.textContent.trim();
      container.textContent = ''; // Clear the initial content
      
      editors[lang] = monaco.editor.create(container, {
          value: defaultValue,
          language: lang,
          theme: 'vs-dark',
          automaticLayout: true,
          minimap: { enabled: false },
          scrollBeyondLastLine: false,
          fontSize: 14,
          lineNumbers: 'on',
          roundedSelection: true,
          scrollbar: {
              vertical: 'hidden',
              horizontal: 'hidden',
              handleMouseWheel: true
          }
      });
  });

  // Handle window resize for proper editor layout
  window.addEventListener('resize', () => {
      editorContainers.forEach(lang => {
          if (editors[lang]) {
              editors[lang].layout();
          }
      });
  });

  // Run button functionality
  document.getElementById('runBtn').addEventListener('click', async () => {
      const outputFrame = document.getElementById('output');
      const testResults = document.getElementById('testResults');
      
      try {
          // Get editor values
          const html = editors.html.getValue();
          const css = editors.css.getValue();
          const js = editors.js.getValue();
          const php = editors.php.getValue();
          
          // Update iframe with HTML/CSS/JS
          outputFrame.srcdoc = `
              <!DOCTYPE html>
              <html>
                  <head>
                      <style>${css}</style>
                      <meta charset="UTF-8">
                  </head>
                  <body>${html}
                      <script>${js}</script>
                  </body>
              </html>
          `;
          
          // Execute PHP code via AJAX
          testResults.textContent = "Running tests...";
          
          const response = await fetch('hooks/run-php.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                  code: php,
                  challenge_id: document.getElementById('challenge-select').value
              })
          });
          
          const result = await response.json();
          
          // Display test results
          if (result.success) {
              testResults.innerHTML = `<span class="text-green-400">✓ Tests passed!</span>\n${result.output}`;
          } else {
              testResults.innerHTML = `<span class="text-red-400">✗ Tests failed</span>\n${result.error || result.output}`;
          }
          
      } catch (error) {
          testResults.textContent = `Error: ${error.message}`;
          console.error('Execution error:', error);
      }
  });

  // Initialize with default values from PHP
  function initializeEditorValues() {
      editorContainers.forEach(lang => {
          const container = document.getElementById(`${lang}Editor`);
          const defaultValue = container.dataset.value || '';
          if (editors[lang]) {
              editors[lang].setValue(defaultValue);
          }
      });
  }
  
  // Call initialization
  initializeEditorValues();
});