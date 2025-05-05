# Challngr

**Challngr** is a web-based, interactive code challenge platform built with PHP, TailwindCSS, and Monaco Editor. It dynamically fetches coding challenges written in Markdown from a MySQL database, renders them in a responsive dashboard layout, and provides an in-browser code editor and executor with real-time feedback.

## Features

- **Dashboard Layout**: Sidebar navigation (Quiz, Code Challenge), header with search and logout.
- **Markdown-Based Challenges**: Challenges stored in MySQL as Markdown, rendered via [Parsedown](https://github.com/erusev/parsedown).
- **Monaco Editor Integration**: Syntax highlighting, autocompletion, bracket matching for HTML, CSS, JS, and PHP.
- **Live Execution**: Inline HTML/CSS/JS execution in an iframe and PHP execution via a secure `run-php.php` endpoint.
- **30-Minute Timer**: Sticky, glassmorphic countdown timer that disables the editor when time is up.
- **Responsive & Themed**: Dark mode, TailwindCSS utility classes, soft rounded corners, and glass backdrop effects.

## Tech Stack

- **Backend**: PHP 8+, PDO for database
- **Frontend**: TailwindCSS, Monaco Editor, Vanilla JS
- **Database**: MySQL (or compatible)
- **Markdown Parser**: Parsedown

## Installation

1. **Clone the repository**
    ```bash
    git clone https://github.com/yourusername/challngr.git
    cd challngr
    ```

2. **Install PHP dependencies**
    ```bash
    composer install
    ```

3. **Configure Database**
    - Create a MySQL database named `challngr`.
    - Run the SQL in `database/schema.sql` to create the `challenges` table.
    - Update the DSN, username, and password in `index.php`:
      ```php
      $dsn  = 'mysql:host=localhost;dbname=challngr;charset=utf8mb4';
      $user = 'db_user';
      $pass = 'db_pass';
      ```

4. **Seed Sample Challenges**
    ```sql
    INSERT INTO challenges (title, content_markdown) VALUES
      ('Factorial Function', '### Factorial Function\nWrite a PHP function `factorial($n)` to compute the factorial of a number.');
    ```

5. **Run the Application**
    ```bash
    php -S localhost:8000
    ```
    Then open `http://localhost:8000` in your browser.

## Usage

1. Select **Code Challenge** from the sidebar.
2. Read the problem statement rendered from Markdown.
3. Write your solution in the Monaco editor panes (HTML/CSS/JS for front-end, PHP for back-end logic).
4. Click **Run Code** to execute and view outputs.
5. The 30-minute timer will count down; once time expires, the Run button is disabled.

## Project Structure

```
challngr/
├── assets/
│   └── editor.js           # Monaco editor initialization and Run logic
├── vendor/                 # Composer dependencies (Parsedown)
├── run-php.php             # Secure PHP code executor endpoint
├── form-handler.php        # Example form submission handler
├── index.php               # Main dashboard and challenge renderer
├── database/
│   └── schema.sql          # SQL schema for `challenges` table
└── README.md               # Project documentation
```

## Contributing

1. Fork the repository.
2. Create a new branch: `git checkout -b feature/YourFeature`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature/YourFeature`.
5. Open a Pull Request.

## License

This project is licensed under the MIT License. See `LICENSE` for details.
