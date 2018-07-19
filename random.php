<?php

$servername = "localhost";
$username = "root";
$password = "root@321";
$dbname = "label";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function generateThai() {
    $characters = 'เป็นมนุษย์สุดประเสริฐเลิศคุณค่า กว่าบรรดาฝูงสัตว์เดรัจฉาน
    จงฝ่าฟันพัฒนาวิชาการ อย่าล้างผลาญฤๅเข่นฆ่าบีฑาใคร
    ไม่ถือโทษโกรธแช่งซัดฮึดฮัดด่า หัดอภัยเหมือนกีฬาอัชฌาสัย
    ปฏิบัติประพฤติกฎกำหนดใจ พูดจาให้จ๊ะๆ จ๋าๆ น่าฟังเอย ฯ';
    
    return substr($characters, 0, (rand(20, 40)));
}


$sql = "SELECT * FROM orders order by id asc";
$result = $conn->query($sql);
$resultIndex = $result->num_rows;

if ($result->num_rows > 0) {
    $i = 0; //setting loop index
    while ($item = $result->fetch_assoc()) {
        $i++;

        $id = $item["id"];
        $address = generateThai();
        echo $id. '<br>';
        // prepare and bind
        $stmt = $conn->prepare("UPDATE orders SET address = ? WHERE id = ?");
        $stmt->bind_param("si", $address, $id);

        // set parameters and execute
        
        $stmt->execute();

    }

}

?>