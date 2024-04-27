<?php
class Receipts{

    private $conn;
    
    public function __construct(){
        // Database connection configuration
        $servername = "localhost"; // MySQL server address
        $username = "admin"; // MySQL username
        $password = ""; // MySQL password

        try {
            // Create a new PDO instance to establish a database connection
            $this->conn = new PDO("mysql:host=$servername;dbname=test", $username, $password);
            
            // Set the PDO error mode to exception to enable error handling
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if the table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE 'receipts'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                $sql = "CREATE TABLE IF NOT EXISTS receipts (
                    Id INT AUTO_INCREMENT PRIMARY KEY,
                    Receipt_No INT UNIQUE NOT NULL,
                    Receipt_Date DATE NOT NULL,
                    Amount INT NOT NULL,
                    Amount_In_Words VARCHAR(255) NOT NULL,
                    Section INT NOT NULL,
                    Remark VARCHAR(255) NOT NULL,
                    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (Section) REFERENCES sections(Id)
                )";
                $this->conn->exec($sql);
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function createReceipt($receiptNo, $receiptDate, $amount, $amountInWords, $section, $remark) {
        $receiptNo = isset($receiptNo) ? $receiptNo : '';
        $receiptDate = isset($receiptDate) ? $receiptDate : '';
        $amount = isset($amount) ? $amount : '';
        $amountInWords = isset($amountInWords) ? $amountInWords : '';
        $section = isset($section) ? $section : '';
        $remark = isset($remark) ? $remark : '';
    
        try {

            // Check if the user with the given ID exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM sections WHERE Id = ?");
            $stmt->execute([$section]);
            $sectionExists = $stmt->fetchColumn() > 0;
    
            if ($sectionExists) {
                $sql = "INSERT INTO Receipts (Receipt_No, Receipt_Date, Amount, Amount_In_Words, Section, Remark)
                VALUES ('$receiptNo', '$receiptDate', '$amount', '$amountInWords', '$section', '$remark')";

                $this->conn->query($sql);
                $message = "Receipt created Successfully.";
                $status = true;
            }else{
                $message = "Section not Found in DB.";
                $status = false;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if(strpos($e->getMessage(), 'Receipt_No') !== false){
                    $message = "Receipt No already exists.";
                }
            } else {
                $message = "Receipt creation failed.";
            }
            // $message = $e->getMessage(); //"Receipt creation failed.";
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    

    public function updateReceiptById($id, $receiptNo, $receiptDate, $amount, $amountInWords, $section, $remark){
        $receipt_no = isset($receiptNo) ? $receiptNo : '';
        $receipt_date = isset($receiptDate) ? $receiptDate : '';
        $amount = isset($amount) ? $amount : '';
        $amount_in_words = isset($amountInWords) ? $amountInWords : '';
        $section = isset($section) ? $section : '';
        $remark = isset($remark) ? $remark : '';
    
        try {
            // Check if the user with the given ID exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM receipts WHERE Id = ?");
            $stmt->execute([$id]);
            $receiptsExists = $stmt->fetchColumn() > 0;
    
            if ($receiptsExists) {
                // User exists, so prepare the update query
                $sql = "UPDATE receipts SET ";
                $params = [];
                if (!empty($receipt_no)) {
                    $sql .= "Receipt_No = ?, ";
                    $params[] = $receipt_no;
                }
                if (!empty($receipt_date)) {
                    $sql .= "Receipt_Date = ?, ";
                    $params[] = $receipt_date;
                }
                if (!empty($amount)) {
                    $sql .= "Amount = ?, ";
                    $params[] = $amount;
                }
                if (!empty($amount_in_words)) {
                    $sql .= "Amount_In_Words = ?, ";
                    $params[] = $amount_in_words;
                }
                if (!empty($section)) {
                    $sql .= "Section = ?, ";
                    $params[] = $section;
                }
                if (!empty($remark)) {
                    $sql .= "Remark = ?, ";
                    $params[] = $remark;
                }
                // Remove the trailing comma and space
                $sql = rtrim($sql, ', ');
                $sql .= " WHERE Id = ?";
                $params[] = $id;

                if(!empty($section)){
                    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM sections WHERE Id = ?");
                    $stmt->execute([$section]);
                    $sectionExists = $stmt->fetchColumn() > 0;

                    if ($sectionExists) {
                        // Execute the update query
                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute($params);
                        $message = "Receipt details updated successfully.";
                        $status = true;
                    }else{
                        $message = "Section not Found in DB.";
                        $status = false;
                    }
                }else{
                    
                        // Execute the update query
                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute($params);
                        $message = "Receipt details updated successfully.";
                        $status = true;
                }
            }else{
                $message = "Receipt not found in DB.";
                $status = false;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if(strpos($e->getMessage(), 'Receipt_No') !== false){
                    $message = "Receipt No already exists.";
                }
            } else {
                $message = $e->getMessage(); //"Receipt creation failed.";
            }
            $status = false;
        }
    
        $response = array(
            'status' => $status,
            'status_message' => $message
        );
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getAllReceipts(){
        $sql = "SELECT * FROM receipts";
        // $data = $this->conn->query($sql);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    
        // Fetch ledger data
        $ledgerData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $message = "Successfully Get All Receipts.";
		$status = true;		

        $response = array(
            'status' => $status,
            'status_message' => $message,
            'data' => $ledgerData
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getReceipts($page = 1, $limit = 10, $search = null, $sortBy = 'CreatedAt', $sortOrder = 'DESC', $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT r.*, r.Id AS rId, s.*
                FROM Receipts r
                LEFT JOIN Sections s ON r.Section = s.Id";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(r.Receipt_No LIKE ? OR r.Amount_In_Words LIKE ? OR r.Remark LIKE ? OR s.section_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    
        // Handle date range
        if ($fromDate !== null) {
            $conditions[] = "r.CreatedAt >= ?";
            $params[] = $fromDate;
        }
        if ($toDate !== null) {
            $conditions[] = "r.CreatedAt <= ?";
            $params[] = $toDate;
        }
    
        // Combine conditions
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Handle sorting
        if ($sortBy !== null) {
            $sql .= " ORDER BY r.$sortBy $sortOrder";
        }
    
        // Calculate offset for pagination
        $offset = ($page - 1) * $limit;
    
        // Add limit and offset to SQL query
        $sql .= " LIMIT $limit OFFSET $offset";

        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch receipt data
        $receiptData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Get total count of receipts (for pagination)
        $totalCount = $this->getTotalReceiptCount($search, $fromDate, $toDate);
    
        // Calculate total pages
        $totalPages = ceil($totalCount / $limit);
    
        // Prepare response data
        $response = array(
            'data' => $receiptData,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages
        );
    
        // Set response header to indicate JSON content
        header('Content-Type: application/json');
    
        // Encode response data as JSON and echo it
        echo json_encode($response);
    }
    
    
    public function getTotalReceiptCount($search = null, $fromDate = null, $toDate = null) {
        // Initialize variables to store SQL conditions and parameters
        $conditions = array();
        $params = array();
    
        // Base SQL query
        $sql = "SELECT COUNT(*) AS total FROM Receipts";
    
        // Handle search
        if ($search !== null) {
            $conditions[] = "(Receipt_No LIKE ? OR Amount_In_Words LIKE ? OR Remark LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    
        // Handle date range
        if ($fromDate !== null) {
            $conditions[] = "CreatedAt >= ?";
            $params[] = $fromDate;
        }
        if ($toDate !== null) {
            $conditions[] = "CreatedAt <= ?";
            $params[] = $toDate;
        }
    
        // Combine conditions
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Prepare and execute SQL query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    
        // Fetch total count
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Return total count
        return $result['total'];
    }
    

    public function deleteReceipt($receiptId) {		
		if($receiptId) {			
			$sql = "
				DELETE FROM receipts 
				WHERE Id = '".$receiptId."'";	 
			if($this->conn->exec($sql)) {
				$messgae = "Receipt delete Successfully.";
				$status = true;			
			} else {
				$messgae = "Receipt delete failed.";
				$status = false;			
			}		
		} else {
			$messgae = "Invalid request.";
			$status = false;
		}
		$response = array(
			'status' => $status,
			'status_message' => $messgae
		);
		header('Content-Type: application/json');
		echo json_encode($response);	
	}
    
    
}
?>