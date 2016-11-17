<?php

class DbOperation
{
    //Database connection link
    private $con;

    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';

        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();

        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }


    
     public function getIssuedBookDetails($userid){

        $stmt = $this->con->prepare("SELECT * FROM borrowers WHERE userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $borrowernum = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $var = $borrowernum['borrowernumber'];

        $stmt = $this->con->prepare("SELECT biblio.title, firstname, surname, borrowers.sort1, issues.issuedate, issues.lastreneweddate , issues.date_due 
                                     FROM issues LEFT JOIN borrowers ON borrowers.borrowernumber=issues.borrowernumber 
                                     LEFT JOIN items ON issues.itemnumber=items.itemnumber 
                                     LEFT JOIN biblio ON items.biblionumber=biblio.biblionumber 
                                     WHERE borrowers.borrowernumber=? AND issues.returndate IS NULL
                                     ");
        $stmt->bind_param("i",$var); 
        $stmt->execute();
        $res = $stmt->get_result();
        $i = 0;
        
        while($borrower = $res->fetch_assoc())
        {
            $books[$i++] = $borrower;
        }
        $stmt->close();
        return $books;
    }

    

    public function getFine($userid){

        $stmt = $this->con->prepare("SELECT * FROM borrowers WHERE userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $temp = $stmt->get_result()->fetch_assoc();

        $stmt = $this->con->prepare("SELECT borrowers.cardnumber, round(Sum(accountlines.amountoutstanding),2) AS 'total owed' 
                                     FROM accountlines LEFT JOIN borrowers ON (accountlines.borrowernumber=borrowers.borrowernumber) 
                                     WHERE borrowers.borrowernumber=?
                                     GROUP BY accountlines.borrowernumber HAVING sum(accountlines.amountoutstanding) > 0");
                                    
        $stmt->bind_param("i",$temp['borrowernumber']);
        $stmt->execute();
        $Fine = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $Fine;
 
    }
    
    
     public function getBook($keyword)
     {

        $stmt = $this->con->prepare("SELECT * FROM biblio
                                    LEFT JOIN biblioitems on (biblioitems.biblionumber = biblio.biblionumber)
                                    WHERE  biblio.title LIKE CONCAT('%', ?, '%')
                                    ");
        $stmt->bind_param("s",$keyword);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $book;
    }

}

?>