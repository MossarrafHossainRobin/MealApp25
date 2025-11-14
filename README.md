<div align="center">
  <h1>🍽️ Meal App 25</h1>
  <p><em>A Web Application for Managing Bazar, Meal Count, Duty Assignment, and Flat Management</em></p>
</div>

<hr />

<div>
  <h2>📌 Overview</h2>
  <p>
    <strong>Meal App 25</strong> is a web-based system designed to manage meal-related operations in shared flats or hostels.<br />
    Users can track bazar purchases, record meal counts, assign duties, and maintain overall flat management efficiently.<br />
    Built with PHP and MySQL for backend processing and a responsive frontend using HTML, CSS, Bootstrap, and JavaScript.
  </p>
  <p>
    The application provides dynamic interactivity via AJAX and structured modular PHP files for easy maintenance and scalability.
  </p>
</div>

<hr />

<div>
  <h2>✨ Key Features</h2>
  <ul style="list-style-type: disc; padding-left: 20px;">
    <li><strong>Bazar Management:</strong> Add and track bazar purchases with date, amount, and assigned buyer.</li>
    <li><strong>Meal Count Tracking:</strong> Record daily meal counts for each member with summaries.</li>
    <li><strong>Duty Assignment:</strong> Assign and manage responsibilities like cooking or cleaning among members.</li>
    <li><strong>Flat Overview:</strong> Central dashboard to view meal, bazar, and duty summaries.</li>
    <li><strong>Notifications:</strong> Alerts for meal entries, bazar updates, and other activities.</li>
    <li><strong>Responsive UI:</strong> Bootstrap-powered interface ensures mobile and desktop usability.</li>
    <li><strong>AJAX Integration:</strong> Real-time updates without page reloads for smooth user experience.</li>
  </ul>
</div>

<hr />

<div>
  <h2>🛠️ Technology Stack</h2>
  <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse;">
    <thead style="background-color: #f4f4f4; text-align: left;">
      <tr>
        <th>Technology</th>
        <th>Role</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>PHP</strong></td>
        <td>Backend logic, session & user management</td>
      </tr>
      <tr>
        <td><strong>MySQL</strong></td>
        <td>Database management for meals, bazar, and members</td>
      </tr>
      <tr>
        <td><strong>HTML5</strong></td>
        <td>Semantic page structure</td>
      </tr>
      <tr>
        <td><strong>CSS3</strong></td>
        <td>Visual styling & layout</td>
      </tr>
      <tr>
        <td><strong>Bootstrap</strong></td>
        <td>Responsive UI components & grid system</td>
      </tr>
      <tr>
        <td><strong>JavaScript & AJAX</strong></td>
        <td>Dynamic interactivity and real-time updates</td>
      </tr>
    </tbody>
  </table>
</div>

<hr />

<div>
  <h2>🚀 Getting Started</h2>
  <ol>
    <li>Clone this repository to your local machine:
      <pre><code>git clone https://github.com/MossarrafHossainRobin/MealApp25/</code></pre>
    </li>
    <li>Install and run a local server environment (e.g., <strong>XAMPP</strong> or <strong>WAMP</strong>).</li>
    <li>Place the project files in the server root directory (e.g., <code>htdocs</code>).</li>
    <li>Navigate to <code>http://localhost/MealApp25/index.php</code> in your browser.</li>
    <li>Start managing meals, bazar, and flat duties using the intuitive dashboard.</li>
  </ol>
</div>

<hr />

<div>
  <h2>📁 Project File Structure</h2>
  <pre style="background:#f4f4f4; padding:10px; border-radius:5px; font-family: monospace;">
MealApp25/
├── ajax/
│   └── load_meals.php          # Load meal data dynamically via AJAX
├── config/
│   └── database.php            # Database connection configuration
├── includes/
│   ├── footer.php              # Footer template
│   ├── header.php              # Header template
│   ├── navigation.php          # Navigation menu
│   └── notifications.php       # Notification messages
├── process/
│   ├── actions.php             # Form handling and CRUD actions
│   └── get_bazar_data.php      # Fetch bazar-related data
├── sections/
│   ├── bazar.php               # Bazar management page
│   ├── home.php                # Dashboard / home page
│   ├── mealcount.php           # Meal count management page
│   ├── members.php             # Member management page
│   ├── settlement.php          # Settlement / account reconciliation
│   ├── summary.php             # Summary of meals and bazar
│   └── water.php               # Water supply management page
├── index.php                   # Main entry point
  </pre>
</div>

<hr />

<div align="center">
  <h2>👨‍💻 Author</h2>
  <p><strong>Mossarraf Hossain Robin</strong></p>
  <p>🎓 CSE Undergraduate Student, Green University of Bangladesh</p>
  <p>
    <a href="mailto:mossarrafhossainrobin@gmail.com" target="_blank" rel="noopener">
      <img src="https://img.shields.io/badge/Email-D14836?style=flat-square&logo=gmail&logoColor=white" alt="Email" />
    </a>
    &nbsp;&nbsp;
    <a href="https://linkedin.com/in/mossarrafhossainrobin" target="_blank" rel="noopener">
      <img src="https://img.shields.io/badge/LinkedIn-0A66C2?style=flat-square&logo=linkedin&logoColor=white" alt="LinkedIn" />
    </a>
    &nbsp;&nbsp;
    <a href="https://github.com/MossarrafHossainRobin" target="_blank" rel="noopener">
      <img src="https://img.shields.io/badge/GitHub-181717?style=flat-square&logo=github&logoColor=white" alt="GitHub" />
    </a>
  </p>
</div>

<hr />

<div align="center">
  <p>
    This project is licensed under the <a href="https://opensource.org/licenses/MIT" target="_blank" rel="noopener">MIT License</a> — feel free to use, modify, and distribute it freely.
  </p>
</div>
