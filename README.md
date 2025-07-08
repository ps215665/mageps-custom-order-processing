<h1>Custom Order Processing for Magento 2</h1>

<p><strong>Vendor_CustomOrderProcessing</strong> is a Magento 2 module that provides a secure REST API to update order statuses using order increment IDs. It logs every order status change and sends an email notification when an order is marked as shipped.</p>

<hr>

<h2>✅ Features</h2>
<ul>
  <li>🔒 <strong>Secure REST API</strong> to update order status (<code>/V1/order/updatestatus</code>)</li>
  <li>📝 Logs <strong>order ID</strong>, <strong>old status</strong>, <strong>new status</strong>, and <strong>timestamp</strong> to a custom DB table</li>
  <li>✉️ Automatically sends <strong>"Order Shipped" email</strong> to the customer if status changes to <code>shipped</code></li>
  <li>📐 Built with <strong>SOLID principles</strong>, <strong>PSR-4</strong>, and <strong>Magento best practices</strong></li>
  <li>⚙️ Uses <strong>Observer</strong>, <strong>Repository</strong>, and <strong>Service Contracts</strong> patterns</li>
  <li>🔧 Supports Magento ACL (<code>Magento_Sales::sales</code>) — token-protected</li>
</ul>

<hr>

<h2>🔌 API Endpoint</h2>

<p><strong>Endpoint:</strong><br>
<code>POST /rest/V1/order/updatestatus</code></p>

<p><strong>Headers:</strong></p>
<pre><code>Authorization: Bearer &lt;admin_or_integration_token&gt;
Content-Type: application/json
</code></pre>

<p><strong>Request Body:</strong></p>
<pre><code>{
  "incrementId": "100000123",
  "newStatus": "pending_admin"
}
</code></pre>

<hr>

<h2>🔐 ACL Permission Required</h2>
<p>The endpoint requires <code>Magento_Sales::sales</code> ACL. Use a valid admin or integration access token.</p>

<hr>

<h2>🏗️ Installation</h2>

<ol>
  <li>Place the module in your Magento root:
    <pre><code>app/code/Vendor/CustomOrderProcessing</code></pre>
  </li>
  <li>Run the following CLI commands:
    <pre><code>
bin/magento module:enable Vendor_CustomOrderProcessing
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
    </code></pre>
  </li>
  <li>(Optional) Generate <code>db_schema_whitelist.json</code>:
    <pre><code>
bin/magento setup:db-declaration:generate-whitelist --module-name=Vendor_CustomOrderProcessing
    </code></pre>
  </li>
</ol>

<hr>

<h2>📬 Email Notification Template</h2>

<p>If order status changes to <code>completed</code>, an email is triggered using:</p>

<ul>
  <li><strong>Template ID:</strong> <code>order_shipped_email_template</code></li>
</ul>

<p>Modify the template under:
<code>view/frontend/email/order_shipped.html</code></p>

<hr>

<h2>🛠️ Architectural Highlights</h2>

<h3>✅ SOLID Principles</h3>
<ul>
  <li><strong>Single Responsibility:</strong> Status logger, notifier, and API controller are separated.</li>
  <li><strong>Dependency Injection:</strong> No object manager usage. All dependencies are injected via constructor.</li>
  <li><strong>Interface Segregation:</strong> Follows Service Contract pattern with <code>Api/</code> interfaces.</li>
  <li><strong>Open/Closed Principle:</strong> New actions can be added without modifying core logic.</li>
</ul>

<h3>✅ Design Patterns</h3>
<ul>
  <li><strong>Observer Pattern:</strong> Used to track and log changes via <code>sales_order_save_after</code> event.</li>
  <li><strong>Repository Pattern:</strong> Used for saving status log via ResourceModel, not direct SQL.</li>
  <li><strong>Service Contracts:</strong> Interfaces exposed for APIs and business logic.</li>
  <li><strong>TransportBuilder:</strong> Used for sending transactional emails.</li>
</ul>

<h3>✅ Code Quality</h3>
<ul>
  <li>Namespaced under <code>Vendor\CustomOrderProcessing</code></li>
  <li>PSR-4 autoloading compliant</li>
  <li>No usage of deprecated code or direct database connections</li>
</ul>

<hr>

<h2>📋 Database Table (order_status_log)</h2>

<table>
  <thead>
    <tr>
      <th>Field</th>
      <th>Type</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>log_id</td>
      <td>INT (PK)</td>
      <td>Auto-increment ID</td>
    </tr>
    <tr>
      <td>order_id</td>
      <td>INT</td>
      <td>Magento order ID</td>
    </tr>
    <tr>
      <td>old_status</td>
      <td>VARCHAR(32)</td>
      <td>Previous order status</td>
    </tr>
    <tr>
      <td>new_status</td>
      <td>VARCHAR(32)</td>
      <td>Updated order status</td>
    </tr>
    <tr>
      <td>changed_at</td>
      <td>TIMESTAMP</td>
      <td>Time of status change</td>
    </tr>
  </tbody>
</table>

<p>Created via <code>db_schema.xml</code> — no need for InstallSchema scripts.</p>

<hr>

<h2>🧪 Example CURL Usage</h2>

<pre><code>
curl -X POST "https://your-magento-site.com/rest/V1/order/updatestatus" \
  -H "Authorization: Bearer &lt;your-admin-token&gt;" \
  -H "Content-Type: application/json" \
  -d '{
    "incrementId": "100000123",
    "newStatus": "pending_admin"
  }'
</code></pre>

<hr>


<h2>👤 Author</h2>
<p><strong>Prakul Kumar Sharma</strong><br>
