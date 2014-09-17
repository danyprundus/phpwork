<?php
include 'inc/bootstrap.php';
error_reporting(0);

if(!isset($_GET['search'])){
	exit('Line: ' . __LINE__);
	}

function get($set){
	global $patent;
	$data = json_decode(@file_get_contents("http://ops.epo.org/3.1/rest-services/published-data/{$patent['type']}/epodoc/{$patent['number']}/{$set}.json"));
	if(!$data){
		exit("Oops! Something went wrong. Please try again later.\n<small>Debug: {$patent['type']}/{$patent['number']}/{$set}</small>");
		}
	if(!isset($data->{'ops:world-patent-data'})){
		return false;
		}
	return $data->{'ops:world-patent-data'};
	}
function to_array($data){
	if(is_array($data)){
		return $data;
		}
	return array($data);
	}
function format_date($d){
	$y = substr($d, 0, 4);
	$m = substr($d, 4, 2);
	$d = substr($d, 6);
	return date('jS F Y', mktime(0, 0, 0, $m, $d, $y));
	}
function format_patent($p){
	return substr($p, 0, 2) . ' ' . substr($p, 2, 4) . '/' . substr($p, 6);
	}
function word_count($text){
	return str_word_count(str_replace('-', '', $text));
	}
function pr($a, $exit = false){
	echo '<pre>'; print_r($a); echo '</pre>'; if($exit){ exit; }
	}

$q = $_GET['search'];
$q = strtoupper($q);
$q = str_replace('/', '', $q);
$q = str_replace(' ', '', $q);
if(substr($q, 0, 3) != 'PCT'){
	$patent['type'] = 'publication';
	$patent['id'] = $patent['number'] = $q;
	}
else{
	$patent['type'] = 'application';
	$patent['id'] = substr($q, 3);
	$patent['number'] = 'WO' . substr($q, 5, 4) . substr($q, 3, 2) . substr($q, 10);
	}
$patent['id'] = format_patent($patent['id']);


// biblio
$data = current(to_array(get('biblio')->{'exchange-documents'}->{'exchange-document'}));
if(isset($data->{'@status'}) && $data->{'@status'} == 'not found'){
	exit('Patent not found');
	}
foreach(to_array($data->abstract) as $a){
	$patent['words_num'] = 0;
	$patent['language'] = $a->{'@lang'};
	if($patent['language'] == 'en'){
		break;
		}
	else{
		foreach(to_array($a->p) as $r){
			$patent['words_num'] += word_count($r->{'$'});
			}
		}
	}
$data = $data->{'bibliographic-data'};

$patent['type'] = 'publication';
$patent['number'] = $data->{'publication-reference'}->{'document-id'}[1]->{'doc-number'}->{'$'};

$patent['date'] = format_date($data->{'application-reference'}->{'document-id'}[1]->date->{'$'});
$patent['title'] = to_array($data->{'invention-title'});
$patent['title'] = $patent['title'][0]->{'$'};

$patent['priority_claims_num'] = 0;
$claims = to_array($data->{'priority-claims'}->{'priority-claim'});
$patent['priority_claims_num'] = count($claims);
$patent['priority_claim'] = format_patent($claims[0]->{'document-id'}[0]->{'doc-number'}->{'$'});
$patent['priority_date'] = format_date($claims[0]->{'document-id'}[0]->date->{'$'});

$patent['applicants'] = array();
foreach($data->parties->applicants->applicant as $applicant){
	if($applicant->{'@data-format'} == 'epodoc'){
		$patent['applicants'][] = trim($applicant->{'applicant-name'}->name->{'$'});
		}
	}
$patent['applicants'] = implode("\n", $patent['applicants']);


$patent['inventors'] = array();
foreach($data->parties->inventors->inventor as $inventor){
	if($inventor->{'@data-format'} == 'epodoc'){
		$patent['inventors'][] = trim($inventor->{'inventor-name'}->name->{'$'});
		}
	}
$patent['inventors'] = implode("\n", $patent['inventors']);

//images
$data = to_array(get('images')->{'ops:document-inquiry'}->{'ops:inquiry-result'});
$patent['pages_num'] = 0;
foreach($data as $r1){
	if($patent['pages_num']){
		break;
		}
	foreach(to_array($r1->{'ops:document-instance'}) as $r2){
		if($patent['pages_num']){
			break;
			}
		foreach(to_array($r2->{'ops:document-section'}) as $r3){
			if($r3->{'@name'} == 'SEARCH_REPORT'){
				$patent['pages_num'] = $r3->{'@start-page'} - 1;
				break;
				}
			}
		}
	}

// claims
$data = get('claims')->{'ftxt:fulltext-documents'}->{'ftxt:fulltext-document'}->claims->claim->{'claim-text'};
if(is_array($data)){
	if(trim(current(explode(PHP_EOL, $data[1]->{'$'}))) == 'AMENDED CLAIMS'){
		$claims = explode(PHP_EOL, $data[1]->{'$'});
		}
	else{
		$claims = array();
		foreach($data as $row){
			if($row = trim(strip_tags($row->{'$'}))){
				$claims[] = $row;
				}
			}
		}
	}
else{
	$claims = explode("\n", $data->{'$'});
	}

$patent['claims_num'] = 0;
foreach($claims as $row){
	$point = substr($row, 0, 3);
	$point = str_replace(' ', '', $point);
	$point = str_replace('.', '', $point);
	if(is_numeric($point) && $point > $patent['claims_num']){
		$patent['claims_num'] = $point;
		}
	}

$claims = str_replace(' .', '.', implode("\n", $claims));
foreach(range(0, $patent['claims_num']) as $i){
	$claims = str_replace(" {$i}.", "\n{$i}.", $claims);
	}
$claims = explode("\n", str_replace("\n\n", "\n", $claims));

$patent['dependent_claims_num'] = 0;
foreach($claims as $row){
	if(substr_count($row, 'claim ') || substr_count($row, 'claims')){
		$patent['dependent_claims_num']++;
		}
	}
$patent['dependent_claims_num'] = min($patent['dependent_claims_num'], $patent['claims_num']);
$patent['independent_claims_num'] = $patent['claims_num'] - $patent['dependent_claims_num'];

$patent['words_num'] += word_count(implode(' ', $claims));
$data = to_array(get('description')->{'ftxt:fulltext-documents'}->{'ftxt:fulltext-document'}->description->p);
foreach($data as $row){
	$patent['words_num'] += word_count($row->{'$'});
	}

$patent['filing_year'] = @end(explode(' ', $patent['date']));

$_SESSION['order']['patent'] = $patent;
echo 'ok';
