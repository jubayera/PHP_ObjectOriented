<?php

//Test this in http://www.practical-research.org/prob1.php/Timestamp

class timeClass
{
  public $Timestamp = '';
}

$splitted = explode('/', $_SERVER['PHP_SELF']);

if( $splitted[2] == "Timestamp") 
{
      $myObj = new timeClass;

      $myObj->Timestamp = time();

      $myJSON = json_encode($myObj);

      echo $myJSON;
}

?>

<?php


//Test this in http://www.practical-research.org/prob2.php/Transaction

class TransactionClass
{
  public $TransactionID;
  public $UserID;
  public $CurrencyAmount;
  public $Verifier;
}

class output
{
  public $Success;
}

$splitted = explode('/', $_SERVER['PHP_SELF']);
if( $splitted[2] == "Transaction") 
{
//  echo $splitted[2] . " here";

     $secret_key = 'NwvprhfBkGuPJnjJp77UPJWJUpgC7mLz';

      echo '<form name="postform" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">
                <table id="postarea">
                <tbody>
                    <tr>    <td>Transaction ID:</td><td><input type="text" name="TransactionID"></td></tr>
                    <tr>    <td>User ID:</td><td><input type="text" name="UserID"></td></tr>
                    <tr>    <td>Currency Amount:</td><td><input type="text" name="CurrencyAmount"></td></tr>
                    <tr>    <td>Verifier:</td><td><textarea id="text" rows="5" cols="30" type="text" name="Verifier"></textarea> </td> </tr>
                    <tr>    <td></td><td> <input type="submit" value="Submit Entry"> </td>    </tr>
                </tbody>
                </table>
            </form>';

      if(isset($_POST['TransactionID']) && isset($_POST['UserID']) && isset($_POST['CurrencyAmount']) && isset($_POST['Verifier']))
      {
          //echo $_POST['TransactionID'] . " " . $_POST['UserID'] . " " . $_POST['CurrencyAmount'] . " " . $_POST['Verifier'];
          //$transactionArray = array($_POST['TransactionID'], $_POST['UserID'], $_POST['CurrencyAmount'], $_POST['Verifier']);
          //$transactionJSON = json_encode($transactionArray);
          //echo $transactionJSON;

          $myObjTransaction_Encode = new TransactionClass;

          $myObjTransaction_Encode->TransactionID = $_POST['TransactionID'];
          $myObjTransaction_Encode->UserID = $_POST['UserID'];
          $myObjTransaction_Encode->CurrencyAmount = $_POST['CurrencyAmount'];
          $myObjTransaction_Encode->Verifier = $_POST['Verifier'];

          $transactionJSON = json_encode($myObjTransaction_Encode);
          //echo $transactionJSON;

          $myObjTransaction_Decode = new TransactionClass;
          $myObjTransaction_Decode = json_decode($transactionJSON);
          echo $transactionJSON;
           
          //is_a($myObjTransaction_Decode, 'TransactionClass') 
      }
      else
      {
            echo 'Can accept RAW JSON Input, if not posted through form. This form can accept input through form submission'; 

            //Make sure that it is a POST request.
            if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
                throw new Exception('Request method must be POST!');
            }

            //Make sure that the content type of the POST request has been set to application/json
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
            /*if(strcasecmp($contentType, 'application/json') != 0){
                throw new Exception('Content type must be: application/json');
            }*/
             
            //Receive the RAW post data.
            $content = trim(file_get_contents("php://input"));
                  
            //Attempt to decode the incoming RAW post data from JSON.
            $myObjTransaction_Decode = json_decode($content, true);
            echo $transactionJSON;
             
            //If json_decode failed, the JSON is invalid.
            if(!is_array($myObjTransaction_Decode)) {
                throw new Exception('Received content contained invalid JSON!');
            }
        }

        //print sha1($secret_key.$myObjTransaction_Decode->{'TransactionID'}.$myObjTransaction_Decode->{'UserID'}.$myObjTransaction_Decode->{'CurrencyAmount'});

          if(sha1($secret_key.$myObjTransaction_Decode->{'TransactionID'}.$myObjTransaction_Decode->{'UserID'}.$myObjTransaction_Decode->{'CurrencyAmount'}) == $myObjTransaction_Decode->{'Verifier'})
          {
              $servername = localhost;
              $db_name = "--";
              $db_user = "--";
              $db_password = '--';

              // Create connection
              $conn = new mysqli($servername, $db_user, $db_password, $db_name);

              // Check connection
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              } 

              $sql = "INSERT INTO Prob2_Transaction (TransactionId, UserId, CurrencyAmount)
              VALUES (" . $myObjTransaction_Decode->{'TransactionID'} . ", " . $myObjTransaction_Decode->{'UserID'} . ", " . $myObjTransaction_Decode->{'CurrencyAmount'} . ")";

              if ($conn->query($sql) === TRUE) {
                  echo "<br/>New record created successfully.<br/><br/>"; //Transaction ID is primary key which has to be unique for each transaction

                  $myObjTransactionStatus_Encode = new output;
                  $myObjTransactionStatus_Encode->Success = true;

                  $transactionJSON = json_encode($myObjTransactionStatus_Encode);
                  echo $transactionJSON;

              } else {
                  //echo "Error: " . $sql . "<br>" . $conn->error;
                  echo "<br/>Transaction ID not unique, another transaction made with the same ID.<br/><br/>";
                  $myObjTransactionStatus_Encode = new output;
                  $myObjTransactionStatus_Encode->Success = false;

                  $transactionJSON = json_encode($myObjTransactionStatus_Encode);
                  echo $transactionJSON;
              }

              $conn->close();
          }
          else {
            echo "Transaction record verification failed..";
          }
  }      

?>
