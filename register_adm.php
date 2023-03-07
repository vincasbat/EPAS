<?php
session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: login.php");
      exit();
   }

switch (@$_SESSION['grupe'])
{
   				
case "admins":
$naud_el_pastas = $_SESSION['naud_email'];
$par = $_SESSION['pareiskejas'];
$mess_pareiskejas = "<span style='color:green;'> $par </span>";
break;

default:
 header("Location: nera_teisiu.php");
  exit();
break;
}  //switch

 
?>
<!DOCTYPE html>
<html>
<head><title>Naujo naudotojo registracija</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<link rel="stylesheet" type="text/css" href="./style/styles.css">
<link rel="shortcut icon" href="favicon.ico">


<script src="https://unpkg.com/axios/dist/axios.min.js"></script>


<script src="https://unpkg.com/vue@2.6.11/dist/vue.js"></script>


<script src="https://unpkg.com/vee-validate@<3.0.0"></script>
<script>
    Vue.use(VeeValidate); // good to go.
	
</script>


<style>
.form-valid {color: red}

.form-group {padding:3px}


</style>



 </head>
 <body>
<div id="container">
   <div id="header">
<?php
include("header.inc");
echo "</div><div id='nav'>\n";



echo "<a href='adm.php'>Administravimas</a><br />\n";

echo "<p align='center'><img  alt='VPB' src='imgs/vpb-TP.png' /></p>\n";

if(isset($mess_pareiskejas))
{
echo "$mess_pareiskejas<br />\n";
echo "<a href='naud_atsijungimas.php'>Atsijungti</a><br />\n";
}


echo "</div><div id='content'>\n";   //išdėstymas
 
 
 ?>

 <h4>Naujo naudotojo registravimas</h4> <br>

<script>

    const dict = {
        custom: {

        naud_vardas: {
                required: 'Reikia įrašyti vardą',
		alpha: 'Varde negali būti skaičių',
		min: 'Vardą turi sudaryti ne mažiau kaip 3 raidės'
            },
        naud_pavarde: {
                required: 'Įrašykite pavardę',
		alpha: 'Pavardėje negali būti skaičių',
		min: 'Pavardę turi sudaryti ne mažiau kaip 3 raidės'
            },
	naud_email: {
                required: 'Reikia įrašyti el.  pašto adresą',
		email: 'Įrašykite teisingą el. pašto adresą'
            },
	naud_passw: {
                required: 'Įrašykite slaptažodį',
		min: 'Slaptažodis turi būti sudarytas iš ne mažiau kaip 6 simbolių',
		max: 'Slaptažodis turi būti sudarytas iš ne daugiau kaip 10 simbolių'
            },
        conf_passw: {
		 required: 'Pakartokite slaptažodį',
		 confirmed: 'Neatitinka slaptažodžio'
		
            },
	naud_telef: {
                required: 'Įrašykite telefoną',
		min: 'Per trumpas telefono numeris'
            },
 	naud_adr: {
                required: 'Reikia įrašyti adresą',
		min: 'Įrašykite visą adresą'
            },
       naud_org: {
                required: 'Įrašykite organizacijos pavadinimą'	
            },
	naud_grupe: {
                required: 'Pasirinkite grupę'
            }



        }//custom
    };

</script>

<div id='vueapp'>

<form @submit.prevent="submitFiles" autocomplete="off">    
<fieldset style='border: 1px solid #000000; border-color: grey;'>
 <legend>VINCASOFT</legend>

<div class="form-group">
      <label>Vardas</label>
        <input type="text" id="naud_vardas"  v-validate="'alpha|required|min:3'" name="naud_vardas"  v-model="naud_vardas" ref="naud_vardas" size='40' maxlength='65' />
	<span class="form-valid">{{ errors.first('naud_vardas') }}</span>
</div>	
<div class="form-group">
      <label>Pavardė</label>
        <input type="text" id="naud_pavarde"  v-validate="'alpha|required|min:3'" name="naud_pavarde"  v-model="naud_pavarde" ref="naud_pavarde" size='40' maxlength='65' />
	<span class="form-valid">{{ errors.first('naud_pavarde') }}</span>
</div>

<div class="form-group">
      <label>El. paštas</label>
        <input autocomplete="off" type="text" v-validate="{ required: true, email: true }" id="naud_email" name="naud_email" v-model="naud_email" ref="naud_email" size='40' maxlength='65' />  
	<span class="form-valid">{{ errors.first('naud_email') }}</span>
</div>

<div class="form-group">
      <label>Slaptažodis </label>
        <input type="password" id="first"  v-validate="'required|min:6|max:10'" name="naud_passw"  v-model="naud_passw" ref="naud_passw" size='40' maxlength='65' />
	<span class="form-valid">{{ errors.first('naud_passw') }}</span>
	
</div>

<div class="form-group">
      <label>Pakartoti</label>
        <input type="password" id="conf_passw"  v-validate="'required|confirmed:naud_passw'" name="conf_passw"  v-model="conf_passw" ref="conf_passw" size='40' maxlength='65' />
	<span class="form-valid">{{ errors.first('conf_passw') }}</span> 
          
</div>

<div class="form-group">
      <label>Telefonas </label>
        <input type="text" v-validate="{ required: true, min:5 }" id="naud_telef" name="naud_telef" v-model="naud_telef" ref="naud_telef" size='40' maxlength='65' />  
	<span class="form-valid">{{ errors.first('naud_telef') }}</span>
</div>

<div class="form-group">
      <label>Adresas</label>
        <input type="text" id="naud_adr"  v-validate="'required|min:6'" name="naud_adr"  v-model="naud_adr" ref="naud_adr"size='40' maxlength='65'  />
	<span class="form-valid">{{ errors.first('naud_adr') }}</span>
	
</div>

<div class="form-group">
      <label>Organizacija</label>
        <input type="text" id="naud_org"  v-validate="'required'" name="naud_org"  v-model="naud_org" ref="naud_org" size='40' maxlength='65' />
	<span class="form-valid">{{ errors.first('naud_org') }}</span>
</div>

<div class="form-group">
      <label>Grupė </label>
<select name="naud_grupe" id="naud_grupe" type="text"  v-validate="{ required: true}"  v-model="naud_grupe" ref="naud_grupe"> 
<option value='' ></option>
<option value='par' >PAR</option>
<option value='pz'>PZ</option>
<option value='pr'>PR</option>
<option value='is'>IS</option>
<option value='ap'>AP</option>
<option value='admins'>ADMINS</option>
</select>
	<span class="form-valid">{{ errors.first('naud_grupe') }}</span>
</div>


<div class="form-group"> <br>
<button style="margin-left: 22%; margin-bottom: .5em" id="btn"  @click.prevent="submitFiles()" type="submit" :disabled="!isFormValid  || errors.any()"  > Registruoti </button> 

<button @click.prevent="valyti()">Trinti</button>

<span id="msg" class="form-valid"></span>

</div>


</fieldset>
</form>

</div>
<br>

 <script>

var app = new Vue({        
  el: '#vueapp', 

 data:{
       
naud_vardas:'',
naud_pavarde:'',
naud_email:'',
naud_passw:'',
conf_passw:'', 
naud_telef:'',
naud_adr:'',
naud_org:'',
naud_grupe:''

    },

  mounted: function () {   
this.$validator.localize('en', dict);  //https://stackoverflow.com/questions/50148858/how-to-localize-error-messages-in-vuejs-using-vee-validation  
document.getElementById("btn").disabled = true; 
    console.log('mounted');
  },

  methods: {

valyti(){
this.naud_vardas='';
this.naud_pavarde='';
this.naud_email='';
this.naud_passw='';
this.conf_passw=''; 
this.naud_telef='';
this.naud_adr='';
this.naud_org='';
this.naud_grupe='';
this.$validator.reset();

},


   
submitFiles() {

let formData = new FormData();
formData.append('naud_vardas', this.naud_vardas);
formData.append('naud_pavarde', this.naud_pavarde);
formData.append('naud_email', this.naud_email);
formData.append('naud_passw', this.naud_passw);
formData.append('conf_passw', this.conf_passw);
formData.append('naud_telef', this.naud_telef);
formData.append('naud_adr', this.naud_adr);
formData.append('naud_org', this.naud_org);
formData.append('naud_grupe', this.naud_grupe);

axios.post('register_adm_post.php', formData,  {  headers: {'Content-Type': 'multipart/form-data' }} ) 
.then(response =>{  
console.log(JSON.stringify(response.data));
if(response.data.rez == "OK"){
document.getElementById("msg").innerHTML = "Naudotojas sukurtas";
this.valyti();
} else {
console.log('Nepavyko:' + JSON.stringify(response.data));
document.getElementById("msg").innerHTML = "Nepavyko: " + response.data.msg;
}

})
.catch(err => {console.log('Nepavyko err');
console.log(JSON.stringify(err));
document.getElementById("msg").innerHTML = "Nepavyko sukurti naudotojo. " + JSON.stringify(error);
 });
}
   
  }, //methods

computed: {
isFormValid() {return this.naud_vardas.length > 0 && this.naud_pavarde.length > 0 && this.naud_email.length > 0 
&& this.naud_passw.length > 0 && this.conf_passw.length > 0 && this.naud_telef.length > 0 && this.naud_adr.length > 0 && this.naud_org.length > 0 
&& this.naud_grupe.length > 0 ; }
}//computed



})   //vue


</script>







<a href='naud_adm.php'>Į naudotojų sąrašą</a><br />
<br />
par - pareiškėjai<br />
admins - administratoriai<br />
pz, pr, is, ap - VPB darbuotojai<br />

 <?php
echo "</div>  <div id='footer'>\n";
include("footer.inc");
?>
 </div>
</div>
</body></html>

