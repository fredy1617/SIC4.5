<!--Adjuntando lo archivos js-->
<script src="../js/jquery-3.1.1"></script>
<script src="../js/materialize.min.js"></script>
<link type="text/css" rel="stylesheet" href="../css/materialize.css" />
<script>
 $('.dropdown-button2').dropdown({
      inDuration: 300,
      outDuration: 225,
      constrain_width: false, // Does not change width of dropdown to that of the activator
      hover: true, // Activate on hover
      gutter: ($('.dropdown-content').width()*3)/2.5 + 5, // Spacing from edge
      belowOrigin: false, // Displays dropdown below the button
      alignment: 'left' // Displays dropdown with edge aligned to the left of button
    }
  );
$('.button-collapse').sideNav({
      menuWidth: 347, 
      edge: 'left',
      closeOnClick: false,
      draggable: true 
    }
  );

$('.modal').modal();

$(document).ready(function(){
    $('.tooltipped').tooltip({delay: 50});
  });

$(document).ready(function(){
    $('ul.tabs').tabs();
  });

 $(document).ready(function(){
    $('.slider').slider();
});
$(document).ready(function(){
  $('.materialboxed').materialbox();
});  

 var toastElement = document.querySelector('.toast');
  var toastInstance = M.Toast.getInstance(toastElement);
  toastInstance.dismiss();  
</script>
