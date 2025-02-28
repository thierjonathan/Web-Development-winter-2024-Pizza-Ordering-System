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
class Backer extends Page
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
        // to do: fetch data for this view from the database
        $sql = "SELECT
        o.ordering_id,
        oa.ordered_article_id,
        oa.article_id,
        oa.status
        FROM ordering o
        LEFT JOIN ordered_article oa ON o.ordering_id = oa.ordering_id";

        $recordset = $this->_database->query($sql);
        if(!$recordset){
            throw new Exception("Abfrage fehlgeschlagen" . $this->_database->error);
        }

        $result = [];

        while ($record = $recordset->fetch_assoc()){
            $ordering_id = $record['ordering_id'];

            if(!isset($result[$ordering_id])){
                $result[$ordering_id] = [
                    'ordering_id' => $ordering_id,
                    'ordered_articles' => []
                ];
            }

            $result[$ordering_id]['ordered_articles'][] = [
                'ordered_article_id' => $record['ordered_article_id'],
                'article_id' => $record['article_id'],
                'status' => $record['status']
            ];
            };

        $recordset->free();

        // Filter orders where all article are either bestellt (0), im Ofen(1), or finished (2)
        foreach($result as $ordering_id => $order){
            $allOrdersInPreparation = true;

            foreach($order['ordered_articles'] as $article){
                if ($article['status'] > 2){
                    $allOrdersInPreparation = false;
                    break;
                }
            }

            if (!$allOrdersInPreparation) {
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
    protected function generateView():void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Backer');
        echo <<< HTML
        <h1 class="title">Backer</h1>
        <script>
            setTimeout(function() {
            window.location.reload();
            }, 10000);
        </script>
        HTML;

        if(!empty($data)){
            foreach($data as $value){
                $ordering_id = htmlspecialchars($value['ordering_id']);
                
                echo <<<HTML
                <section>
                    <div class="order-id-box">
                        <h2>Order ID: {$ordering_id}</h2>
                    </div>
                    <h3 id="ordered-pizza_{$ordering_id}" class="ordered_pizza">Ordered Pizzas:</h3>

                HTML;

                $pizzaIsWithDriver = false;
    
                foreach ($value['ordered_articles'] as $key => $article){
                    $showStatus = "Unbekannt";
    
                    switch($article['status']){
                        case 0:
                            $showStatus = "Bestellt";
                            break;
                        case 1:
                            $showStatus = "Im Ofen";
                            break;
                        case 2:
                            $showStatus = "Fertig";
                            break;
                        case 3:
                            $showStatus = "Unterwegs";
                            break;
                        case 4:
                            $showStatus = "Geliefert";
                            break;
                        default:
                            $showStatus = "Unknown Status";
                            break;
                    }

                    if($article['status'] > 2){
                        $pizzaIsWithDriver = true;
                    }

                    if(!$pizzaIsWithDriver){
                        echo <<<HTML
                        <div class="pizza-box">
                        <h4>Pizza ID: {$article['ordered_article_id']}</h4>
                        <h5 id="current-status_{$article['ordered_article_id']}"> current status: {$showStatus}</h5>
                        <p>Change Status:</p>
                        <form method="post" id="changeStatus_{$article['ordered_article_id']}" action="backer.php">
                            <input type="hidden" name="ordered_article_id" value="{$article['ordered_article_id']}">
                            
                            <input type="radio" id="bestelltButton_{$article['ordered_article_id']}" name="status" value="0"
                                onclick="this.form.submit();" >
                            <label for="bestelltButton_{$article['ordered_article_id']}">Bestellt</label>

                            <input type="radio" id="imOfenButton_{$article['ordered_article_id']}" name="status" value="1"
                                onclick="this.form.submit();">
                            <label for="imOfenButton_{$article['ordered_article_id']}">Im Ofen</label>

                            <input type="radio" id="fertigButton_{$article['ordered_article_id']}" name="status" value="2"
                                onclick="this.form.submit();">
                            <label for="fertigButton_{$article['ordered_article_id']}">Fertig</label>
                        </form>
                        </div>
                        HTML;
                    }else{
                        echo <<<HTML
                        <p>Pizza ID {$article['ordered_article_id']} wurde vom Fahrer mitgenommen.</p>
                        HTML;
                    }
                }
                echo <<<HTML
                </section>
                <hr class="dashed">
                HTML;

            }
        } else {
            echo <<<HTML
                <p> Kein Pizzas zum Zubereiten </p>
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
    protected function processReceivedData():void
    {
        /*
        *0: Bestellt 
        *1: Im Ofen
        *2: Fertig
        */
        parent::processReceivedData();
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isset($_POST['ordered_article_id'])){
            $status = (int)$_POST['status'];
            $ordered_article_id = (int) $_POST['ordered_article_id'];

            $updateStatusSQL = "UPDATE ordered_article SET status = $status WHERE ordered_article_id = $ordered_article_id";

            if(!$this->_database->query($updateStatusSQL)){
                throw new Exception("Fehler bei der Statusanderung: " . $this->_database->error);
            }
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
            $page = new Backer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Backer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >