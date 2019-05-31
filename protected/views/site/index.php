<?php $this->pageTitle=Yii::app()->name; ?>
<script type="text/javascript">	
	var inds = configJs.current.activeTab;			
			if(inds =='1'){		checkContainer5(); 	checkContainer6(); 	checkContainer7();  	}			
	    	if(inds =='2'){		checkContainer8(); 	checkContainer15(); checkContainer16();	checkContainer21(); 	checkContainer24(); checkContainer25(); 
				checkContainer26(); checkContainer27();  }
 	$(document).on('click', '.ui-tabs-nav li', function(e) {
	    	var href = $('.links li.selected a').attr('href');   	var currentController = configJs.current.controller;
	    	var ind = parseInt($(this).find('a').attr('href').split('_').slice(-1)[0]);
	    	if(ind =='1'){ 		checkContainer5();	checkContainer6(); 	checkContainer7(); 	}			
	    	if(ind =='2'){	    checkContainer8();	checkContainer15();  checkContainer16();	 checkContainer21(); 	checkContainer24(); checkContainer25(); checkContainer26(); checkContainer27();  }
	    	$.ajax({type: "POST",url: configJs.urls.baseUrl + '/site/rememberTab', dataType: "json",
	    	  	data: {url : href, controller : currentController, index: ind},
	    	  	success: function(data) {
	    	  			console.log(data);
	      		}  	});    })
</script>
<div class="dashboard mytabs hidden"><?php if (true) {	$tabs = array();
			foreach ($dashboards as $dashboard){ $tabs[$dashboard['name']] = $this->renderPartial('dashboard', array('id' => $dashboard['id'] ,'activesubtab'=>$activesubtab+1), true);		}
			$this->widget('CCustomJuiTabs', array('tabs' => $tabs, 'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab',),
			    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',));	} else {	echo "Welcome to SNSit!";	} ?></div>