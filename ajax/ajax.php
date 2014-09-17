<?
include '../inc/bootstrap.php';
$action=$_POST['action'];
switch($action)
{
    case 'calculate': echo CalculateAmount($_POST['classes']);break;
    
}
function CalculateAmount($Classes)
{
    
    global $db;
    $tmp=explode(',',$Classes);
    $NoOfMarks=count($_SESSION['mark_step1']['TMType']);
    
    
    foreach($tmp as $val)
    {
    if($val!='')
    {
        preg_match_all('/\[([A-Za-z0-9 ]+?)\]/', $val, $out);
       
        $ClasseseID[$out[1][1]][]=$out[1][0];
        $RegionsID[$out[1][1]]=$out[1][1];
    }    
        
    }
//print_r($RegionsID ); 
$constants = array(
	'bank_spread' => BANK_SPREAD,
	'priority_claims' => $order['patent']['priority_claims_num'],
	'pages' => $order['patent']['pages_num'],
	'claims' => $order['patent']['claims_num'],
	'independent_claims' => $order['patent']['independent_claims_num'],
	);
$order['currency']='USD';    
foreach($db->query("SELECT * FROM currencies")->fetchAll(PDO::FETCH_ASSOC) as $rate){
	$constants[$rate['currency']] = $rate['value'];
	if($rate['currency'] == $order['currency']){
		$_SESSION['order']['exchange_rate'] = $exchange_rate = $rate['value'];
		}
	}
$TotalFees=array();
?>
<table style="width: 200px;;">
<tr>
<th colspan="2">Cost</th>
</tr>
<?
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

    //debug
    $debug=array(
    'noOFMarks'=>$NoOfMarks,
    'APPDivisor'=>$AppDivisor[0]['ApplicationDivisor'],
    'count(Classes)'=>count($ClasseseID[$RegionID]),
    
    );
    
   //echo '<br>'. $region_name. 
   $NoOfApplications=round($NoOfMarks*ceil((count($ClasseseID[$RegionID])/$AppDivisor[0]['ApplicationDivisor'])),0);
    //print_r($Fees );
    
    $TotalFees[$RegionID]=$NoOfApplications*$Fees[0]['sum']/$exchange_rate;
    ?><tr><td><?=$region_name ?></td><td><?echo $TotalFees[$RegionID];?>	<small>
									<?php echo $order['currency'] ?>
								</small></td></tr><?
    
}
?>
<tr><th>Total</th><th><?=array_sum($TotalFees)?>	<small>
									<?php echo $order['currency'] ?>
								</small></th></tr>
<?
?></table><?   
//return json_encode($TotalFees);

//read formula for 

//
    
    
}
?>