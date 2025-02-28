<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class bestellung extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
	 * @return array An array containing the requested data. 
	 * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        $sql = "SELECT
        a.article_id,
        a.name,
        a.price,
        a.picture
        FROM article a";

    $recordset = $this->_database->query($sql);
    if (!$recordset) {
        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    $result = array();
    while ($record = $recordset->fetch_assoc()) {
        $result[] = $record; // Add each row to the result array
    }

    $recordset->free();
    return $result;

    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
	 * @return void
     */
    protected function generateView():void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('to do: change headline');
        echo <<< HTML
            <h1>Speisekarte</h1>
            <h3>Pizzas: </h3>
        HTML;

        if (!empty($data)) {
            foreach ($data as $value) {
                $article_id = $value['article_id'];
                $name = $value['name'];
                $price = $value['price'];
                $picture = $value['picture'];
                
                echo <<<HTML
                    <!--Ordering ID, Address, Order Time-->
                    <section class="menu-item">
                        <img src="img/{$name}.jpg" data-price={$price} data-article_id={$article_id} 
                        data-name={$name} height="200" width="200" alt="{$name}" onclick="addPizza(this)">
                        <p>{$name}</p>
                        <p>{$price} €</p>
                        <br>
                    </section>
                HTML;
            }
        }
        
        //Warenkorb UI
        echo <<<HTML
        <form id="orderForm" method="post" action="bestellung.php">
            <h2>Warenkorb</h2>
            <select id="warenkorb" name="ordered_articles[]" size="10" tabindex="0" multiple style="min-width: 200px">>
            </select>
        HTML;

        //Initialize Session Variables: Ordering IDs Array and Address
        if (!isset($_SESSION['ordered_articles']) || !is_array($_SESSION['ordered_articles'])) {
            $_SESSION['ordered_articles'] = [];
        }
        if (!isset($_SESSION['address'])) {
            $_SESSION['address'] = '';
        }

        //Get JSON input from AJAX
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        //Input the JSON data to the session variables
        if ($data) {
            if (isset($data['ordered_articles']) && is_array($data['ordered_articles'])) {
                $_SESSION['ordered_articles'] = array_map('intval', $data['ordered_articles']);
            }
            if (isset($data['address'])) {
                $_SESSION['address'] = $data['address'];
            }
        }
        

        //Price Ausgabe, Buttons UI
        echo <<<HTML
                <p id="preisAusgabe">0€</p>
                <label for="address">Ihre Adresse:</label>
                <br>
                <textarea id="address" name="address" tabindex="2" accesskey="a" placeholder="Ihre Adresse hier eingeben" required></textarea>
                <br>
                <button type="button" onclick="clearWarenkorb()">Alle löschen</button>
                <button type="button" onclick="clearSelectedItems()">Auswahl löschen</button>
                <!-- <input type="radio" name="submit_button" onclick="document.forms['formid'].submit();"> -->
                <input id="submitOrder" type="submit" value="Bestellen" disabled>
        </form>
        HTML;

        echo <<<HTML
            <script src="request.js"></script>
            <script src="bestellung.js"></script>
        HTML;
        $this->generatePageFooter();
    }
    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
	 * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address']) && isset($_SESSION['ordered_articles'])) {
            $address = $this->_database->real_escape_string($_POST['address']);
            $ordering_time = date('Y-m-d H:i:s');  // Current date and time
            $ordered_articles = $_SESSION['ordered_articles'];  // Array of article IDs
    
            // Step 1: Get the latest ordering_id and increment by 1
            $result = $this->_database->query("SELECT MAX(ordering_id) AS max_id FROM ordering");
            $row = $result->fetch_assoc();
            $new_ordering_id = $row['max_id'] + 1;

            $_SESSION ['all_ordering_id'][] = $new_ordering_id; //Added the new order id to the session variable

    
            // Step 2: Insert the new order into 'ordering' table
            $insertOrderSQL = "
                INSERT INTO ordering (ordering_id, address, ordering_time)
                VALUES ($new_ordering_id, '$address', '$ordering_time')
            ";
            
            if (!$this->_database->query($insertOrderSQL)) {
                throw new Exception("Error inserting new order: " . $this->_database->error);
            }
    
            // Step 3: Get the latest ordered_article_id and start from the next ID
            $result = $this->_database->query("SELECT MAX(ordered_article_id) AS max_article_id FROM ordered_article");
            $row = $result->fetch_assoc();
            $next_ordered_article_id = $row['max_article_id'] + 1;
    
            // Step 4: Insert each ordered article
            foreach ($ordered_articles as $article_id) {
                $article_id = (int)$article_id;  // Ensure ID is an integer
            
                $insertArticleSQL = "
                    INSERT INTO ordered_article (ordered_article_id, ordering_id, article_id, status)
                    VALUES ($next_ordered_article_id, $new_ordering_id, $article_id, 0)
                ";
                if (!$this->_database->query($insertArticleSQL)) {
                    throw new Exception("Error inserting ordered article: " . $this->_database->error);
                }
                $next_ordered_article_id++;
            }
        }
    }

    public static function main():void
    {
        try {
            $page = new bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}
bestellung::main();