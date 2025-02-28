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
    protected function getViewData():array
    {
        //From table 'ordering' get ordering_id, address, ordering_time
        $sql = "SELECT 
        o.ordering_id, 
        o.address, 
        o.ordering_time, 
        oa.ordered_article_id, 
        oa.article_id, 
        oa.status
        FROM ordering o
        LEFT JOIN ordered_article oa ON o.ordering_id = oa.ordering_id";

        $recordset = $this->_database->query($sql);
        if(!$recordset){
            throw new Exception("Abfrage fehlgeschlagen" . $this->_database->error);
        }

        $result = array();
        $record = $recordset->fetch_assoc();
        while ($record) {
            $ordering_id = $record['ordering_id'];
    
            // If the ordering_id not exists, create new entry
            if (!isset($result[$ordering_id])) {
                $result[$ordering_id] = [
                    'ordering_id' => $record['ordering_id'],
                    'address' => $record['address'],
                    'ordering_time' => $record['ordering_time'],
                    'ordered_articles' => [] 
                ];
            }
    
            // Add ordered articles to the ordered_articles array
            $result[$ordering_id]['ordered_articles'][] = [
                'ordered_article_id' => $record['ordered_article_id'],
                'article_id' => $record['article_id'],
                'status' => $record['status']
            ];
    
            $record = $recordset->fetch_assoc();
            };
            
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
    protected function generateView(): void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Fahrer Seite');
        echo "<h1>Fahrer</h1>";

    
        if (!empty($data)) {
            foreach ($data as $value) {
                $ordering_id = $value['ordering_id'];
                $address = $value['address'];
                $ordering_time = $value['ordering_time'];

                echo <<<HTML
                    <!--Ordering ID, Adress, Bestellungszeit-->
                    <section>
                    <h3>Bestellung ID: {$ordering_id}</h3>
                    <h4>Adresse: {$address}</h4>
                    <p>Bestellungszeit: {$ordering_time}</p>
                    <br>
                    <h4>Bestellte Pizza</h4>
                HTML;
    
                // Initializing the $allOrdersReady variable
                $allOrdersReady = true;
    
                foreach ($value['ordered_articles'] as $key => $article) {
                    $status_text = "Unbekannt";
                
                    switch ($article['status']) {
                        case 0:
                            $status_text = "Bestellt";
                            break;
                        case 1:
                            $status_text = "Im Ofen";
                            break;
                        case 2:
                            $status_text = "Fertig";
                            break;
                        case 3:
                            $status_text = "Unterwegs";
                            break;
                        case 4:
                            $status_text = "Geliefert";
                            break;
                        default:
                            $status_text = "Unbekannter Status"; 
                            break;
                    }
                
                    echo <<<HTML
                    <!--Ordered Articles-->
                        <h5>Ordered Pizza ID: {$article['ordered_article_id']}</h5>
                        <p>Status: {$status_text}</p>
                    <br>
                    HTML;
    
                    if ($article['status'] < 2) { //If article is not 'fertig'
                        $allOrdersReady = false;
                    }
                }
                
                $disabled = $allOrdersReady ? "" : "disabled";
    
                echo <<<HTML
                <!--Update Button-->
                <form method="post" action="fahrer.php">
                    <input type = "hidden" name = "ordering_id" value="{$ordering_id}">
                    <label for="status">Status wählen:</label>
                    <select name="status" size="1" tabindex="1" id="status">
                        <option value="0" disabled selected>Wählen Sie ein Status</option>
                        <option value="3">Unterwegs</option>
                        <option value="4">Geliefert</option>
                    </select>
                    <button type="submit" {$disabled}>Update Status</button>
                    <br>
                </form>
                </section>
                HTML;
            }
        }        
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
    }

        //header('Location: fahrer.php');
        //exit;
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