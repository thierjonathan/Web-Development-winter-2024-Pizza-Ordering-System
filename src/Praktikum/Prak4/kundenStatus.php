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
class kundenStatus extends Page
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
    if (empty($_SESSION['all_ordering_id'])) {
        return []; // Return empty result if there is no pizza
    }

    if (!isset($_SESSION['all_ordering_id'])){
        echo "No Session Found"; // If no session found
        return [];
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
        o.ordering_id,
        o.address
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
                'address' => $record['address'],
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
        header("Content-Type: application/json; charset=UTF-8");
        $data = $this->getViewData(); //NOSONAR ignore unused $data

        if (empty($data)){
            echo json_encode([]);
            return;
        }
        echo json_encode($data);
    }

    protected function processReceivedData():void //prevent cache (copy-pasted code from Praktikumsblatt)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 01 Jul 2000 06:00:00 GMT"); // Datum in der Vergangenheit
        header("Cache-Control: post-check=0, pre-check=0", false); // fuer IE
        header("Pragma: no-cache");
        session_cache_limiter('nocache'); // VOR session_start()!
        session_cache_expire(0);
    }

    public static function main():void
    {
        try {
            $page = new kundenStatus();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo json_encode(["error" => $e->getMessage()]);
            //for debug:
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
    }
}

kundenStatus::main();
