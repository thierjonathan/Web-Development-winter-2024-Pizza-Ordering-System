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
class Fahrer extends Page
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
    protected function getViewData(): array
{
    // Query to retrieve data from ordering, ordered_article, and article tables
    $sql = "SELECT 
        o.ordering_id, 
        o.address, 
        o.ordering_time, 
        oa.ordered_article_id, 
        oa.article_id, 
        oa.status,
        a.price
    FROM ordering o
    LEFT JOIN ordered_article oa ON o.ordering_id = oa.ordering_id
    LEFT JOIN article a ON oa.article_id = a.article_id;";

    $recordset = $this->_database->query($sql);
    if (!$recordset) {
        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    $result = [];

    while ($record = $recordset->fetch_assoc()) {
        $ordering_id = $record['ordering_id'];

        // If the ordering_id does not exist in the result, create a new entry
        if (!isset($result[$ordering_id])) {
            $result[$ordering_id] = [
                'ordering_id' => $ordering_id,
                'address' => $record['address'],
                'ordering_time' => $record['ordering_time'],
                'ordered_articles' => []
            ];
        }

        // Add ordered article details to the 'ordered_articles' array
        $result[$ordering_id]['ordered_articles'][] = [
            'ordered_article_id' => $record['ordered_article_id'],
            'article_id' => $record['article_id'],
            'status' => $record['status'],
            'price' => $record['price']
        ];
    }

    $recordset->free();

    // Filter orders where all articles are either finished (status = 2) or delivered (status = 4)
    foreach ($result as $ordering_id => $order) {
        $allOrdersReady = true;

        foreach ($order['ordered_articles'] as $article) {
            if ($article['status'] < 2 || $article['status'] == 4) {
                $allOrdersReady = false;
                break;
            }
        }

        // Remove orders that are not fully ready or delivered
        if (!$allOrdersReady) {
            unset($result[$ordering_id]);
        }
    }

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
    protected function generateView(): void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Fahrer');
        echo <<< HTML
        <h1 class="title">Fahrer</h1>
        <script>
            setTimeout(function() {
            window.location.reload();
            }, 10000);
        </script>
        HTML;

    
        if (!empty($data)) {
            foreach ($data as $value) {
                $ordering_id = htmlspecialchars($value['ordering_id']);
                $address = htmlspecialchars($value['address']);
                $ordering_time = htmlspecialchars($value['ordering_time']);
                
                //Calculate Gesamtpreis
                $gesamtPreis = 0.0;
                foreach ($value['ordered_articles'] as $article){
                    $gesamtPreis += $article['price'];
                }
                $gesamtPreis = number_format($gesamtPreis, 2, '.', '');

                //Get the status of order
                $status_num = $value['ordered_articles'][0]['status'];
                switch ($status_num) {
                    case 2:
                        $status_text = "Fertig";
                        break;
                    case 3:
                        $status_text = "Unterwegs";
                        break;
                    default:
                        $status_text = "Unbekannter Status"; 
                        break;
                }
                
                echo <<<HTML
                    <!--Ordering ID, Adress, Bestellungszeit-->
                    <h2 class="bestellung-id">Bestellung ID: {$ordering_id}</h2>
                    <section class="container">
                    <div class="row">
                        <div class="box column bestellung-box">
                            <h3>Adresse: {$address}</h3>
                            <p>Bestellungszeit: {$ordering_time}</p>
                            <p>Gesamtpreis: {$gesamtPreis}</p>
                            <p>Status: {$status_text}
                            <br>
                        </div>
                        <div class="box column pizza-box">
                            <h4>Bestellte Pizza: </h4>
                            <ul>

                HTML;

    
                foreach ($value['ordered_articles'] as $key => $article) {            
                    echo <<<HTML
                    <!--Ordered Articles-->
                        <li>Pizza ID: {$article['ordered_article_id']}</li>
                HTML;
                }
    
                echo <<<HTML
                        </ul>
                        </div>
                    </div>

                <!--Update Button-->
                <div class="box status-box">
                <form method="post" action="fahrer.php">
                    <input type = "hidden" name = "ordering_id" value="{$ordering_id}">
                    <h3>Status wählen:</h3>
                    <br>
                            <input type="hidden" name="ordered_article_id" value="{$article['ordered_article_id']}">
                            
                            <input type="radio" id="unterwegsButton_{$article['ordered_article_id']}" name="status" value="3"
                                onclick="this.form.submit();">
                            <label for="unterwegsButton_{$article['ordered_article_id']}">Unterwegs</label><br>

                            <input type="radio" id="geliefertButton_{$article['ordered_article_id']}" name="status" value="4"
                                onclick="this.form.submit();">
                            <label for="geliefertButton_{$article['ordered_article_id']}">Geliefert</label><br>
                    <br>
                </form>
                </div>
                <hr class="dashed">
                </section>
                HTML;
            }
        } else {
            echo <<<HTML
                <p>keine Bestellung auszuliefern</p>
            HTML;
        } 
        $this->generatePageFooter();
    }
    



    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
	 * @return void
     */
    protected function processReceivedData(): void
{
    /*  status 0: bestellt
               1: im ofen
               2: fertig 
               3: unterwegs
               4: geliefert
    */
    
    parent::processReceivedData();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isset($_POST['ordering_id'])) {
        $status = (int) $_POST['status'];
        $ordering_id = (int) $_POST['ordering_id'];

        // Update the status in the 'ordered_article' table instead of 'ordering'
        $updateStatusSQL = "UPDATE ordered_article SET status = $status WHERE ordering_id = $ordering_id";
        
        if (!$this->_database->query($updateStatusSQL)) {
            throw new Exception("Fehler bei der Statusänderung: " . $this->_database->error);
        }

        header('Location: fahrer.php');
        exit;
    }
}


    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
	 * @return void
     */
    public static function main():void
    {
        try {
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >