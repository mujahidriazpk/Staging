<h1>Event tracking script</h1>

<h2>Usage</h2>
<em>client</em> – the second party where the event is happening, e.g. the shop where the sale is made.
<ol>
    
    <li>Copy the code under <em>Script</em> into a new .php file.</li>
    <li>This file needs to be implemented and accessible on the client’s site.</li>
    <li>The code under <em>Tracking Code</em> needs to be added to the page where the event is happening, e.g. checkout.</li>
    <li>The <code>$response</code> variable must contain the path to the php file.</li>
    <li>The client must set the <code>$event</code> data with his own (dynamic) values.</li>
</ol>
<h2>Tracking Code</h2>
<p>This tracking code should be included on the client’s website with the appropriate data. You find more information about it in the script file.</p>
<pre><code>&lt;?php
$event = array(
    'id' => 'YOUR ID HERE', // any string you like to track the event with
    // ensure prices are floats/ doubles or format them using
    // .. somethings like `(double) sprintf('%.2f', $priceFloat);`
    // .. or `(double) sprintf('%d.%d', $priceInteger / 100, $priceInteger % 100);`
    'price' => $priceAsFloat, // price
    'priceCurrency' => 'USD', // 3-char-code
    'provision' => 10.00, // affiliate’s share
    'provisionCurrency' => 'EUR', // 3-char-code
);
// identification
$apiClientToken = '<?php echo isset($client[1]) ? $client[1] : '%%API_CLIENT_TOKEN%%'; ?>';
$apiClientName = '<?php echo isset($client[0]) ? $client[0] : '%%API_CLIENT_NAME%%'; ?>';
$apiTarget = '<?php echo admin_url('admin-ajax.php'); ?>';
 
$response = include 'path/to/script.php';
?&gt;</code></pre>

<h2>Script</h2>

<pre><code><?php echo htmlspecialchars($template); ?></code></pre>
