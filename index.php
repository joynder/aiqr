<html>
	<head>
		<title>markUP</title>
		<style>
			textarea {
				resize: none;
				border-radius: 25px; 
				border: 10px solid white; 
			}
			
			table.a {
				table-layout: fixed;
			}
			
			div {
				width: 100%;
				word-wrap: break-word;
			}
		
			.example::-webkit-scrollbar {
				display: none;			/* Chrome, Safari and Opera */
			}

			.example {
				-ms-overflow-style: none;  	/* IE and Edge */
				scrollbar-width: none;  	/* Firefox */
			}
			
			body {
				background-color: #363333;
				color: white;
			}
			
			font {
				color: #e16428;
			}
			
			.button {
				background-color: #363333;
				border-radius: 25px; 
				border: 3px solid #363333;
				color: white;
			}
			.button:hover {
				background-color: #e16428;
				border-color: #e16428;
			}
			
			.edges {
				border-radius: 25px; 
				border: 4px 
				solid white; 
			}
		</style>
	</head>
	<body div class="example">
	<!-- MENU -->
		<table border=0 width=100% align=left bgcolor=#272121
		style='border-radius: 10px; border: 4px solid #272121; '><tr>
			<form method="post">
				<td align=left width=50%>
					<input type="submit" name="home" class="button" value="🏠" />
					<input type="submit" name="profilo" class="button" value="👤" />
					<b><font color=009900>< markUP</font>
					<?php
						$username = $_REQUEST['username'];
						echo "@$username ";
					?>
					<font color=009900> ></font></b>
				</td>
				<td align=right width=50%>
					<input type="text" name="ricerca" value="" placeholder="Cerca..." class="edges">
					<input type="submit" name="cerca" class="button" value="🔎"/>
				</td>
<!--				
				<td align=right width=33%>
					<input type="submit" name="new_post" class="button" value="✏️" />
				</td>	
-->	
			</form>
		</tr></table>
		
		<?php
			if(array_key_exists('profilo', $_POST)) { 
				profilo(); 
			} 
			if(array_key_exists('home', $_POST)) { 
				home(); 
			} 
			if(array_key_exists('cerca', $_POST)) { 
				cerca(); 
			} 
			if(array_key_exists('new_post', $_POST)) { 
				new_post(); 
			} 
			
			
			function profilo(){
				$username = $_REQUEST['username'];
				header("location: profilo.php?username=$username");
			}
			
			function home(){
				$username = $_REQUEST['username'];
				header("location: homepage.php?username=$username");
			}
			
			function cerca(){
				$username = $_REQUEST['username'];
				$ricerca = $_POST['ricerca'];
				if ($username==$ricerca) 
					header("location: profilo.php?username=$username");
				else {
					//Variabili per la connessione al database
					$host="localhost";
					$user="root";
					$password="";
					$nome_db="markup";
					
					//Istruzioni della query
					$query="select *
						from Utenti
						where username='$ricerca'
					";
					//Connessione al DBMS
					$connect=mysqli_connect($host, $user, $password) or die
					('Impossibile connettersi al server: ' . mysqli_error());
					mysqli_select_db($connect,$nome_db) or die 
					('Accesso al database non riuscito: ' . mysqli_error());
					//Eseguo la query
					$return_query=mysqli_query($connect,$query);
					$n=mysqli_num_rows($return_query);
					if (!$n) header("location: esplora.php?username=$username");
					else header("location: ricerca.php?username=$username&ricerca=$ricerca");
				}
			}
			
			function new_post(){
				$username = $_REQUEST['username'];
				header("location: new_post.php?username=$username");
			}
		?>
	<!-- MENU -->	
	
	<!-- NUOVO POST -->
		<form method="post">
			<table border=0 align=right bgcolor=#272121 width=30% 
			style='border-radius: 25px; border: 10px solid #272121; height: 100px; width: 100%px;'>
				<caption> &ensp; </caption>
				<tr>
					<td align=left colspan=2>
						<b>Crea un nuovo post </b>
						<br>
					</td>
				</tr>
				<tr>
					<td align=left>
						<input type="text" name="titolo" value="" maxlength="20" required  placeholder="Titolo" class="edges">
					</td>
					<td align=right>
						<input type="submit" name="newpost" class="button" value="✏️" />
					</td>
				</tr>
				<tr>
					<td align=center colspan=2>
						<textarea name="contenuto" rows="10" cols="51"  maxlength="50000"
						required placeholder="Contenuto"></textarea>
					</td>
				</tr>
			</table>
		</form>
		<?php
			//Variabili per la connessione al database
			$host="localhost";
			$user="root";
			$password="";
			$nome_db="markup";
			
			If (isset($_POST["titolo"])){
				//Campi della query
				$dataPost = date("Y-m-d H:i:s");
				$titolo = $_POST['titolo'];
				$contenuto = $_POST['contenuto'];
				$username = $_REQUEST['username'];
				
				//Istruzioni della query
				$query="insert into Post values
						('$dataPost','$titolo','$contenuto','$username','false');
				";
				//Connessione al DBMS
				$connect=mysqli_connect($host, $user, $password) or die
				('Impossibile connettersi al server: ' . mysqli_error());
				mysqli_select_db($connect,$nome_db) or die 
				('Accesso al database non riuscito: ' . mysqli_error());
				//Eseguo la query
				$return_query=mysqli_query($connect,$query);
				if (!$return_query) echo "<font color=#009900>Errore durante il caricamento del nuovo post</font>";
				else header("location: homepage.php?username=$username");
			}
		?>
	<!-- NUOVO POST -->
	
	<!-- CARICAMENTO POST -->
		<?php
			//Variabili per la connessione al database
			$host="localhost";
			$user="root";
			$password="";
			$nome_db="markup";
			
			//Campi della query
			$username = $_REQUEST['username'];
			//Istruzioni della query
			$query="select distinct Post.titolo, Post.contenuto, Post.username, Post.dataPost
					from Follow, Utenti, Post
					where Follow.username=Utenti.username and Utenti.username=Post.username
					and Post.username in (select Follow.username_2 from Follow where Follow.username like '$username')
					and archived = false
					order by dataPost DESC
					LIMIT 100;
			";
			
			$connect=mysqli_connect($host, $user, $password) or die
			('Impossibile connettersi al server: ' . mysqli_error());
			mysqli_select_db($connect,$nome_db) or die 
			('Accesso al database non riuscito: ' . mysqli_error());
			//Eseguo la query
			$re=mysqli_query($connect,$query);
			if (!$re) echo "<font color=#009900>Errore durante il caricamento dei post</font>";
			//Nessun post nella home
			$no_post=mysqli_num_rows($re);
			if (!$no_post) {
				echo "<font color=#009900>Nessun post nella tua home 😿</font>";
				echo "<font color=#009900><br>Prova a crearne uno! ✏️</font>";
				//echo "<br><b><a href='new_post.php?username=$username'><font color=#009900>Prova a crearne uno ✏️</font></a></b><br>";				
			}
			
			
			while($row=mysqli_fetch_array($re)){
				
			echo "<table border=0 width=51% align=left
					bgcolor=#272121
					bordercolor=#272121
					style='border-radius: 25px; border: 10px solid #272121; height: 100px; width: 100%px;'
					class=a
					>"
			;
			echo "<caption> &ensp; </caption>";
				echo "<thead>";
					echo "<tr>";
						echo "<th align=left><div>" .$row["titolo"];
						$timestamp=strtotime("-30 min");
						if ($row["dataPost"]>date("Y-m-d H:i+5:s", $timestamp)) echo " 🔥</div>";
						echo "</th>";
						if($row["username"]==$username) 
							echo "<th align=right><div>" .$row["username"] ."</div></th>";
						else 
							echo "<th align=right><b><a href='ricerca.php?username=$username&ricerca=$row[username]'><font color=#009900><div>$row[username]</div></font></a></b></th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tr>";
					echo "<div><td colspan=2><div>" .$row["contenuto"] ."</div></td>";
				echo "</tr>";
				
			echo "</table>";
			}
			
		?>
	<!-- CARICAMENTO POST -->	
	</body>
</html>