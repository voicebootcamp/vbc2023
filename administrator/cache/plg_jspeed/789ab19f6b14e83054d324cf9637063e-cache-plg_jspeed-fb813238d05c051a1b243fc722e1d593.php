<?php die("Access Denied"); ?>#x#a:2:{s:6:"result";s:287:"try{function cancelRegistration(registrantId)
{var form=document.adminForm;if(confirm("Do you want to cancel this registration ?"))
{form.task.value="registrant.cancel";form.id.value=registrantId;form.submit();}}}catch(e){console.error('Error in script declaration; Error:'+e.message);};";s:6:"output";s:0:"";}