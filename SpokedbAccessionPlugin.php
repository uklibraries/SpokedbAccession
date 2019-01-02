<?php
/**
 * SpokedbAccession
 * 
 * @copyright Copyright 2018 Eric C. Weig 
 * @license GNU General Public License v3.0
 */

/**
 * The SpokedbAccession plugin.
 * 
 * @package Omeka\Plugins\spokedbAccession
 */
    class SpokedbAccessionPlugin extends Omeka_Plugin_AbstractPlugin
    {
    
    // Define Hooks

    protected $_hooks = array(
        'install',
        'uninstall',
	'admin_footer',
	'define_routes'
	);
	
	public function hookInstall()
    {
      
    }
    
    public function hookUninstall()
    {
     
    }
	
	function hookDefineRoutes($args)
    {
    $router = $args['router'];

    }
	
    # To ensure that an interview gets an accession number, the interview
    # must be assigned to a collection first.  The collection must 
    # have a project code assigned.
    #	  
    # Accession Number Format:
    # YYYY+oh+4digitaccessionforyear+_+projectcode+4digitaccessionforproject
    # Example Accession Number: 2019oh0001_dab0001
    
    public function hookAdminFooter(){
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 

        if (strpos($actual_link, '/items/edit/') !== false) {
        $interview_accession = metadata('item', array('General', 'Interview Accession'));
        $collection = get_collection_for_item();
        if ($collection) {
        $collectionId = metadata($collection, 'id');
        }
        if ($interview_accession == NULL && $collectionId !== NULL) {
        $proj = metadata($collection, array('Project', 'Project Code'));
	    $curYear = date('Y');
	    $abrev = "oh";
	    $items = get_records('Item', array(), 30000);
        $all_accessions = array();
	    
	    set_loop_records('items', $items);
	    foreach (loop('items') as $item): 
	    $accnums = metadata('item', array('General','Interview Accession'));
	    $accessions[] = $accnums;
	    endforeach;
	    
	    $numof = array();
	    
	    foreach($accessions as $accession) {
	    if (strpos($accession, $curYear) !== false) {
	    $totalints = substr($accession, 0, 9);
	    $digits = substr($totalints, -3); 
	    $numof[] = $digits;
		}
	    }
    	
    	    if (!empty($numof)) { 
    	    $total_seq = (max($numof));
            } else {
            $total_seq = 0;
            }
    	
	    $numbers = array();
	    
	    foreach($accessions as $accession) {
	    if (strpos($accession, $proj) !== false) {
	    $seqs = substr($accession, -3);
	    $numbers[] = $seqs;
		}
	    }

	    if (!empty($numbers)) { 
    	    $seq = (max($numbers));
            } else {
            $seq = 0;
            }

	    $plus_one = 1;
	    $sum_total_seq = $total_seq + $plus_one;
	    $sum_total_seq = str_pad($sum_total_seq,4,"0",STR_PAD_LEFT);
	    $sum_seq = $seq + $plus_one;
	    $sum_seq = str_pad($sum_seq,4,"0",STR_PAD_LEFT);
	    $oh = "oh";
	    $div = "_";
            $new_accession = $curYear . $oh . $sum_total_seq . $div . $proj . $sum_seq;
            } else { 
            $new_accession = "";
            }
        
    	?>
    	<script>
        var myAccession = document.getElementById("Elements-252-0-text");
        if (myAccession && myAccession.value) {
            console.log("My input has a value!");
        } else  {
        window.addEventListener('load', function() {
        document.getElementById("Elements-252-0-text").value = "<?php echo $new_accession; // HTML ?>";
        });

        }
        </script>
    
        <?php
        } elseif (strpos($actual_link, '/items/edit/') == false) {
        //do nothing
        }    
    
    }
}

