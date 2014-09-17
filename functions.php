<?
function CalculateAmount($Classes,$showOnScreen=true)
{
    global $db,$order;
    $NoOfMarks=count($_SESSION['mark_step1']['TMType']);
    
    while(list($key,$val)=each($Classes))
    {
        $ClasseseID[$key]=$key;
        $RegionsID[$val]=$val;
    }
//print_r($RegionsID ); 
$constants = array(
	'bank_spread' => BANK_SPREAD,
	'priority_claims' => $order['patent']['priority_claims_num'],
	'pages' => $order['patent']['pages_num'],
	'claims' => $order['patent']['claims_num'],
	'independent_claims' => $order['patent']['independent_claims_num'],
	);
  
foreach($db->query("SELECT * FROM currencies")->fetchAll(PDO::FETCH_ASSOC) as $rate){
	$constants[$rate['currency']] = $rate['value'];
	if($rate['currency'] == $order['currency']){
		$_SESSION['order']['exchange_rate'] = $exchange_rate = $rate['value'];
		}
	}
$TotalFees=array();

if(is_array($RegionsID))
foreach($RegionsID as  $RegionID)
{
     $Formula = $db->query('select formula from fees where phase=4 AND region_entity_id='.$RegionID)->fetchAll(PDO::FETCH_ASSOC);        
     $AppDivisor = $db->query('select ApplicationDivisor from region_entities where id='.$RegionID)->fetchAll(PDO::FETCH_ASSOC);
     $sql='select sum(formula)  as sum from fee_amounts  where fee_type_id in(10,11,12) AND region_entity_id='.$RegionID;
     $Fees = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $region_name = $db->query('SELECT * FROM regions where id='.$RegionID)->fetchAll(PDO::FETCH_ASSOC);
    $region_name =$region_name [0]['region'];   
   // echo '<br>4-'.eval('return ' . $fee_value . ';') / $exchange_rate;        
    $Formula=$Formula[0]['formula'];

    
    $NoOfApplications=round($NoOfMarks*(count($ClasseseID)/$AppDivisor[0]['ApplicationDivisor']),0);
    //print_r($Fees );
    
     $TotalFees[$RegionID]=$NoOfApplications*$Fees[0]['sum']/$exchange_rate;
     if($showOnScreen) 
     {
        echo number_format($TotalFees[$RegionID],2);
        
     }
    return $TotalFees[$RegionID];
    
}

//
    
 
}

function GetClassByRegion($selection,$regions)
{
    $ret=array();
  if(is_array($selection)){  
   while(list($key,$val)=each($selection))
   {
    //echo '<br>'.$key; //class ID
    //$val =array of regions ID
    foreach($val as $val1)
    {
        //reset regions and parse then
        reset($regions);
        foreach($regions as $val2)
        {
            if($val1==$val2['id'])
            {
                $ret[$val2['region']][]=$key;
            }
               
            
        }
        
    }
    
    
   }
   }
   return $ret;
}

?>