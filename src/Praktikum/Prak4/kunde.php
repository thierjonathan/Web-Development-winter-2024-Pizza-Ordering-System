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
class kunde extends Page
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
    // $_SESSION['all_ordering_id'] = []; //To test the code
    // $_SESSION['all_ordering_id'][] = 17;

    if (!isset($_SESSION['all_ordering_id']) || empty($_SESSION['all_ordering_id'])) {
        return []; // Return empty result if no orders belong to the user
    }

    $sessionOrderingIds = $_SESSION['all_ordering_id'];
    if (!empty($sessionOrderingIds)){
        $sessionOrderingIds = implode(',', array_map('intval', $_SESSION['all_ordering_id'])); //All orders that belong only to the customer
        $inCondition = "o.ordering_id IN ($sessionOrderingIds)";

    } else {
        $inCondition = "1=0";
    }
    $sql = "SELECT
        a.name,
        a.price,
        oa.ordered_article_id,
        oa.article_id,
        oa.status,
        o.ordering_id
    FROM 
        ordering o
    LEFT JOIN 
        ordered_article oa ON o.ordering_id = oa.ordering_id
    LEFT JOIN 
        article a ON oa.article_id = a.article_id
    WHERE
        ($inCondition)";

    $recordset = $this->_database->query($sql);
    if (!$recordset) {
        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    $result = [];
    while ($record = $recordset->fetch_assoc()) {
        $ordering_id = $record['ordering_id'];

        if (!isset($result[$ordering_id])) {
            $result[$ordering_id] = [
                'ordering_id' => $ordering_id,
                'ordered_articles' => []
            ];
        }

        $result[$ordering_id]['ordered_articles'][] = [
            'ordered_article_id' => $record['ordered_article_id'],
            'article_id' => $record['article_id'],
            'name' => $record['name'],
            'status' => $record['status']
        ];
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
        $this->generatePageHeader('to do: change headline'); //to do: set optional parameters

        echo <<< HTML
        <h2>Ihre Bestellung</h2>
        <div id="ordersContainer"></div>
        HTML;
        if(!empty($data)){
            foreach($data as $value){
                $ordering_id = htmlspecialchars($value['ordering_id']);
                echo <<< HTML
                <body>
                    <h3>Bestellung ID: {$ordering_id}</h3>
                HTML;

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
                
                    $radioName = 'status_' . $article['ordered_article_id'];
                
                    // Output the pizza details
                    echo <<<HTML
                    <section>
                        <h4>Ordered pizza: {$article['name']}</h4>
                        <p id="status_{$article['ordered_article_id']}">Status: {$status_text}</p>
                    </section>
                    <label>
                    HTML;
                
                    $checkedBestellt = $status_text === 'Bestellt' ? 'checked' : '';
                    $checkedImOfen = $status_text === 'Im Ofen' ? 'checked' : '';
                    $checkedFertig = $status_text === 'Fertig' ? 'checked' : '';
                    $checkedUnterwegs = $status_text === 'Unterwegs' ? 'checked' : '';
                    $checkedGeliefert = $status_text === 'Geliefert' ? 'checked' : '';
                
                    // Bestellt
                    echo <<<HTML
                    <input type="radio" name="{$radioName}" value="bestellt" {$checkedBestellt} disabled> Bestellt
                    HTML;
                
                    // Im Ofen
                    echo <<<HTML
                    <input type="radio" name="{$radioName}" value="im Ofen" {$checkedImOfen} disabled> Im Ofen
                    HTML;
                
                    // Fertig
                    echo <<<HTML
                    <input type="radio" name="{$radioName}" value="fertig" {$checkedFertig} disabled> Fertig
                    HTML;
                
                    // Unterwegs
                    echo <<<HTML
                    <input type="radio" name="{$radioName}" value="unterwegs" {$checkedUnterwegs} disabled> Unterwegs
                    HTML;
                
                    // Geliefert
                    echo <<<HTML
                    <input type="radio" name="{$radioName}" value="geliefert" {$checkedGeliefert} disabled> Geliefert
                    HTML;
                
                    echo <<<HTML
                    </label>
                    <br>
                    HTML;
                }                
            }
        } else {
            echo <<< HTML
            <p> Sie haben noch kein Pizzas bestellt </p>
            HTML;
        }

        echo <<< HTML
        <script src="kundenStatus.js"></script>
        HTML;
            
        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

    }

    public static function main():void
    {
        try {
            $page = new kunde();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
    
}

kunde::main();
