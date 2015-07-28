<?php namespace mahbubcsedu\laraturksoap;
/**
 * Created by PhpStorm.
 * User: mahbub
 * Date: 7/28/15
 * Time: 2:36 AM
 */
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class AmazonMechanicalTurk {

    private $MTURK_SERVICE = 'AWSMechanicalTurkRequester';
    private $endpoint;
    private $aws_access_key;
    private $aws_secret_key;
    private $guzzle;
    private $defaults;
    private $sandbox;
    private $mt;

    /**
     * Sets AWS keys, endpoint, and default config values, plus creates Guzzle client for API calls.
     *
     * @throws LaraTurkException if AWS keys are not set
     */
    public function __construct()
    {
        // check if AWS root account keys have been configured
        if ( config('laraturk.credentials.AWS_ROOT_ACCESS_KEY_ID') === false OR
            config('laraturk.credentials.AWS_ROOT_SECRET_ACCESS_KEY') === false )
        {
            throw new LaraTurkException('AWS Root account keys must be set as environment variables.');
        }

        $this->aws_access_key = config('laraturk.credentials.AWS_ROOT_ACCESS_KEY_ID');
        $this->aws_secret_key = config('laraturk.credentials.AWS_ROOT_SECRET_ACCESS_KEY');

        // set endpoint and config defaults to the Production site
        $this->endpoint = 'https://mechanicalturk.amazonaws.com/';
        $this->defaults = config('laraturk.defaults.production');
        $this->sandbox=false;

        $this->guzzle = new Client();
        $this->mt = new MTurkInterface($this->aws_access_key, $this->aws_secret_key, array("sandbox" => $this->sandbox ));
    }

    /**
     * Sets the API in Sandbox Mode.
     * All API calls will go to the sandbox Amazon Mechanical Turk site and will use sandbox default config parameters.
     *
     * @return void
     */
    public function setSandboxMode()
    {
        $this->endpoint = 'https://mechanicalturk.sandbox.amazonaws.com/';
        $this->defaults = array_merge(config('laraturk.defaults.production'), config('laraturk.defaults.sandbox'));
        $this->sandbox=true;
    }

    /**
     * Sets the API in Production Mode.
     * All API calls will go to the production Amazon Mechanical Turk site and will use production default config parameters.
     *
     * @return void
     */
    public function setProductionMode()
    {
        $this->endpoint = 'https://mechanicalturk.amazonaws.com/';
        $this->defaults = config('laraturk.defaults.production');
        $this->sandbox=false;
    }

    /**
     * Creates a HIT based on an existing HITTypeID and HITLayoutID.
     * Reference: http://docs.aws.amazon.com/AWSMechTurk/latest/AWSMturkAPI/ApiReference_CreateHITOperation.html
     *
     * @param array $params
     * @return array
     * @throws LaraTurkException
     */
    /*function turk50_hit($title, $description, $money, $url, $duration, $lifetime, $qualification, $maxAssignments, $keywords, $AutoApprovalDelayInSeconds)
    {


        $turk50 = new TurkSoap ($this->aws_access_key, $this->aws_secret_key, array(
            "sandbox" => $this->sandbox
        ));

        // prepare ExternalQuestion
        $Question = "<ExternalQuestion xmlns='http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2006-07-14/ExternalQuestion.xsd'>" . "<ExternalURL>$url</ExternalURL>" . "<FrameHeight>600</FrameHeight>" . "</ExternalQuestion>";

        // prepare Request
        $Request = array(
            "Title" => $title,
            "Description" => $description,
            "Question" => $Question,
            "Reward" => array(
                "Amount" => $money,
                "CurrencyCode" => "USD"
            ),
            "AssignmentDurationInSeconds" => $duration,
            "LifetimeInSeconds" => $lifetime,
            "QualificationRequirement" => $qualification,
            "MaxAssignments" => $maxAssignments,
            "Keywords" => $keywords,
            "AutoApprovalDelayInSeconds" => $AutoApprovalDelayInSeconds
        );
        $CreateHITResponse = $turk50->CreateHIT($Request);
        return $CreateHITResponse;
    }*/

    function turk_debug() {
        echo "<br /><br />\n\nRawData<br />\n" . $this->mt->RawData . "\n\n<br /><br />";
        echo "<br /><br />\n\nSOAPData<br />\n" . $this->mt->SOAPData . "\n\n<br /><br />";

        echo $this->mt->Fault;
        echo $this->mt->Error;
    }

    function turkSoap_Hit($title,$description,$money,$url,$duration,$lifetime) {
       // turkSoap_Hit('donato sample','description',.05,'http://roc.cs.rochester.edu/donato/tukerpage.php');
        //global array(             "sandbox" => $this->sandbox         ), $DEBUG, $this->aws_access_key, $this->aws_secret_key;



        $this->mt->SetOperation('CreateHIT');
        $this->mt->SetVar('Title', $title);
        $this->mt->SetVar('Description',$description);
        $this->mt->SetVar('Amount',$money);
        $this->mt->SetVar('MaxAssignments',1);
        $this->mt->SetVar('AssignmentDurationInSeconds',$duration);
        $this->mt->SetVar('LifetimeInSeconds',$lifetime);
        $this->mt->SetVar('Question',$url);
        $this->mt->Invoke();

        turk_debug($this->mt);
    }
    /*function turk50_hit($title, $description, $money, $url, $duration, $lifetime, $qualification, $maxAssignments, $keywords, $AutoApprovalDelayInSeconds) {
        global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key;

        if (array(             "sandbox" => $this->sandbox         ))
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key );
        else
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key, array (
                    "sandbox" => FALSE
            ) );

            // prepare ExternalQuestion
        $Question = "<ExternalQuestion xmlns='http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2006-07-14/ExternalQuestion.xsd'>" . "<ExternalURL>$url</ExternalURL>" . "<FrameHeight>600</FrameHeight>" . "</ExternalQuestion>";

        // prepare Request
        $Request = array (
                "Title" => $title,
                "Description" => $description,
                "Question" => $Question,
                "Reward" => array (
                        "Amount" => $money,
                        "CurrencyCode" => "USD"
                ),
                "AssignmentDurationInSeconds" => $duration,
                "LifetimeInSeconds" => $lifetime,
                "QualificationRequirement" => $qualification,
                "MaxAssignments" => $maxAssignments,
                "Keywords" => $keywords,
                "AutoApprovalDelayInSeconds" => $AutoApprovalDelayInSeconds
        );

        // invoke CreateHIT
        $CreateHITResponse = $turk50->CreateHIT ( $Request );
        // $hitId = $CreateHITResponse->HIT->HITId;
        // $assignId = turk_easyHitToAssn($hitId);
        return $CreateHITResponse;
    }

    function turk50_createHITfromData($hit_properties){
        global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key;

        if (array(             "sandbox" => $this->sandbox         ))
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key );
        else
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key, array (
                    "sandbox" => FALSE
            ) );

            // invoke CreateHIT
            $CreateHITResponse = $turk50->CreateHIT ( $hit_properties );
            // $hitId = $CreateHITResponse->HIT->HITId;
            // $assignId = turk_easyHitToAssn($hitId);
            return $CreateHITResponse;
    }*/

    function turkSoap_Approve($asn, $encouragement = "") {
        // venar303@gmail.com account
        //global $DEBUG, array("sandbox" => $this->sandbox), $this->aws_access_key, $this->aws_secret_key;

       // $this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array("sandbox" => $this->sandbox  ) );

        $this->mt->SetOperation ( 'ApproveAssignment' );
        $this->mt->SetVar ( 'AssignmentId', $asn );
        // $this->mt->SetVar('RequesterFeedback', $encouragement);

        $result = $this->mt->Invoke ();

        if (! $this->mt->FinalData ['Request'] ['IsValid']) {
            echo "error with Approval";
            print_r ( $this->mt );
        }
        return $this->mt;
    }



    function turkSoap_Reject($asn, $encouragement = "") {
        // venar303@gmail.com account
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key;

       // $this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array("sandbox" => $this->sandbox ) );

        $this->mt->SetOperation ( 'RejectAssignment' );
        $this->mt->SetVar ( 'AssignmentId', $asn );
        // $this->mt->SetVar('RequesterFeedback', $encouragement);

        $result = $this->mt->Invoke ();

        if (! $this->mt->FinalData ['Request'] ['IsValid'])
            echo "error with Rejection";
        return $this->mt;
    }



    function turkSoap_Bonus($worker_id, $assignment_id, $bonus, $reason) {
       // global array(             "sandbox" => $this->sandbox         ), $DEBUG, $this->aws_access_key, $this->aws_secret_key;
        //$this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'GrantBonus' );
        $this->mt->SetVar ( 'AssignmentId', $assignment_id );
        $this->mt->SetVar ( 'BonusAmount', $bonus );
        $this->mt->SetVar ( 'Reason', $reason );
        $this->mt->SetVar ( 'WorkerId', $worker_id );

        $this->mt->Invoke ();
        return $this->mt;
    }



    function turkSoap_Dispose($hitId) {
        // venar303@gmail.com account
       // global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key;

        //$this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'DisposeHIT' );
        $this->mt->SetVar ( 'HITId', $hitId );
        $result = $this->mt->Invoke ();
        if (! $this->mt->FinalData ['Request'] ['IsValid'])
            echo "error with disposal";

        return $this->mt;
    }




    function turkSoap_SearchHits() {
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

       // $this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'SearchHITs' );
        $this->mt->SortProperty = "CreationTime";
        $this->mt->SortDirection = "Descending";
        $this->mt->setVar ( 'PageSize', $PAGE_SIZE );
        $result = $this->mt->Invoke ();
        // print_r($this->mt->FinalData);
        return $this->mt->FinalData ['HIT'];
    }




    /*function turk50_search($pageNum) {
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        if (array(             "sandbox" => $this->sandbox         ))
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key );
        else
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key, array (
                "sandbox" => FALSE
            ) );

        $Request = array (
            "PageSize" => $PAGE_SIZE,
            "SortProperty" => "Enumeration",
            "PageNumber" => $pageNum
        );

        $search = $turk50->SearchHITs ( $Request );
        // print_r($search);
        if ($search->SearchHITsResult->NumResults == 1)
            return array (
                $search->SearchHITsResult->HIT
            );
        if ($search->SearchHITsResult->NumResults > 0)
            return $search->SearchHITsResult->HIT;
    }



    function turk50_searchReviewable($pageNum) {
       // global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        if (array(             "sandbox" => $this->sandbox         )) {
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key );
        } else {
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key, array (
                "sandbox" => FALSE
            ) );
        }

        $Request = array (
            "PageSize" => $PAGE_SIZE,
            "SortProperty" => "Enumeration",
            "PageNumber" => $pageNum
        );

        $search = $turk50->GetReviewableHITs ( $Request );
        // print_r($search);
        if ($search->GetReviewableHITsResult->NumResults == 1)
            return array (
                $search->GetReviewableHITsResult->HIT
            );
        if ($search->GetReviewableHITsResult->NumResults > 0)
            return $search->GetReviewableHITsResult->HIT;
    }



    function turk50_getNumHits() {
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        if (array(             "sandbox" => $this->sandbox         ))
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key );
        else
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key, array (
                "sandbox" => FALSE
            ) );

        $Request = array (
            "PageSize" => $PAGE_SIZE,
            "SortProperty" => "Enumeration"
        );

        $search = $turk50->SearchHITs ( $Request );
        // print_r($search);
        return $search->SearchHITsResult->TotalNumResults;
    }



    function turk50_getNumReviewableHits() {
      //  global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        if (array(             "sandbox" => $this->sandbox         ))
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key );
        else
            $turk50 = new Turk50 ( $this->aws_access_key, $this->aws_secret_key, array (
                "sandbox" => FALSE
            ) );

        $Request = array (
            "PageSize" => $PAGE_SIZE,
            "SortProperty" => "Enumeration"
        );

        $search = $turk50->GetReviewableHITs ( $Request );
        // print_r($search);
        return $search->GetReviewableHITsResult->TotalNumResults;
    }


    function turk50_searchAllHits() {
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        $totalNumHits = turk50_getNumHits ();
        $numPages = ceil ( $totalNumHits / $PAGE_SIZE );
        $array = turk50_search ( 1 );
        for($i = 2; $i <= $numPages; $i ++) {
            $array = array_merge ( $array, turk50_search ( $i ) );
        }

        return $array;
    }



    function turk50_getAllReviewableHits() {
       // global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        $totalNumHits = turk50_getNumReviewableHits ();
        $numPages = ceil ( $totalNumHits / $PAGE_SIZE );
        $array = turk50_searchReviewable ( 1 );
        for($i = 2; $i <= $numPages; $i ++) {
            $array = array_merge ( $array, turk50_searchReviewable ( $i ) );
        }

        return $array;
    }*/



    function turkSoap_ExpireHit($hitId) {
       // global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key;

       // $this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'ForceExpireHIT' );
        $this->mt->setVar ( 'HITId', $hitId );
        $result = $this->mt->Invoke ();

        print_r ( $this->mt->FinalData );
    }



    function turkSoap_GetReviewable() {
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        //$this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'GetReviewableHITs' );
        $this->mt->setVar ( 'Status', "Reviewable" );
        $this->mt->setVar ( 'PageSize', $PAGE_SIZE );
        $result = $this->mt->Invoke ();

        if (isset ( $this->mt->FinalData ['HIT'] ))
            $x = sizeOf ( $this->mt->FinalData ['HIT'] );
        else
            die ( "No hits to review" );
        $tempArray = array ();
        for($i = 0; $i < $x; $i ++) {
            if (isset ( $this->mt->FinalData ['HIT'] [$i] ))
                $val = $this->mt->FinalData ['HIT'] [$i] ['HITId'] [$i];
            else
                $val = $this->mt->FinalData ['HIT'] ['HITId'];
            $tempArray [] = $val;
        }
        return $tempArray;
    }



    function turkSoap_GetReviewing() {
        //global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key, $PAGE_SIZE;

        //$this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'GetReviewableHITs' );
        $this->mt->setVar ( 'Status', "Reviewing" );
        $this->mt->setVar ( 'PageSize', $PAGE_SIZE );
        $result = $this->mt->Invoke ();

        if (isset ( $this->mt->FinalData ['HIT'] ))
            $x = sizeOf ( $this->mt->FinalData ['HIT'] );
        else
            die ( "No hits to review" );
        $tempArray = array ();
        for($i = 0; $i < $x; $i ++) {
            if (isset ( $this->mt->FinalData ['HIT'] [$i] ))
                $val = $this->mt->FinalData ['HIT'] [$i] ['HITId'] [$i];
            else
                $val = $this->mt->FinalData ['HIT'] ['HITId'];
            $tempArray [] = $val;
        }
        return $tempArray;
    }



    function turkSoap_HitToAssn($hit) {
       // global $DEBUG, array(             "sandbox" => $this->sandbox         ), $this->aws_access_key, $this->aws_secret_key;

        //$this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        $this->mt->SetOperation ( 'GetAssignmentsForHIT' );
        $this->mt->SetVar ( 'HITId', $hit );
        $this->mt->Invoke ();

        return $this->mt->FinalData;
    }

    function turkSoap_getAccountBalance() {
        //$this->mt = new mturkinterface ( $this->aws_access_key, $this->aws_secret_key, array(             "sandbox" => $this->sandbox         ) );

        //$this->mt = new MTurkInterface($this->aws_access_key, $this->aws_secret_key, array("sandbox" => $this->sandbox ));

        /* $Request = array (
                "HITId" => $hitId
        ); */
        $this->mt->SetOperation ( 'GetAccountBalance' );
        $this->mt->Invoke ();
        return $this->mt->FinalData;
    }
}
