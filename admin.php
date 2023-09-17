<?php

@include 'config.php';

if(isset($_POST['add_produto'])){
   $nome = $_POST['nome'];
   $sku = $_POST['sku'];
   $descricao = $_POST['descricao'];
   $preco = $_POST['preco'];
   $estoque = $_POST['estoque'];
   $tipo = $_POST['tipo_de_variacao'];
   $descri = $_POST['descricao_variacao'];
   $p_image = $_FILES['p_image']['name'];
   $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
   $p_image_folder = 'uploaded_img/'.$p_image;

   $insert_query = mysqli_query($conn, "INSERT INTO `produtos`(nome, sku, descricao, preco, estoque, tipo_de_variacao, descricao_variacao, image) VALUES('$nome','$sku', '$descricao', '$preco', '$estoque','$tipo','$descri', '$p_image')") or die('query falhou');

   if($insert_query){
      move_uploaded_file($p_image_tmp_name, $p_image_folder);
      $message[] = 'O produto foi cadastrado';
   }else{
      $message[] = 'o produto nao foi cadastrado';
   }
};



if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_query = mysqli_query($conn, "DELETE FROM `produtos` WHERE id = $delete_id ") or die('query faihou');
   if($delete_query){
      header('location:admin.php');
      $message[] = 'Produto foi deletado';
   }else{
      header('location:admin.php');
      $message[] = 'Produto nao foi deletado';
   };
};

if(isset($_POST['update_produto'])){
   $update_p_id = $_POST['update_p_id'];
   $update_p_nome = $_POST['update_p_nome'];
   $update_p_preco = $_POST['update_p_preco'];
   $update_p_image = $_FILES['update_p_image']['name'];
   $update_p_image_tmp_name = $_FILES['update_p_image']['tmp_name'];
   $update_p_image_folder = 'uploaded_img/'.$update_p_image;


   $update_query = mysqli_query($conn, "UPDATE `produtos` SET nome = '$update_p_nome',  preco = '$update_p_preco', image = '$update_p_image' WHERE id = '$update_p_id'");

   if($update_query){
      move_uploaded_file($update_p_image_tmp_name, $update_p_image_folder);
      $message[] = 'Produto Editado com Sucesso';
      header('location:admin.php');
   }else{
      $message[] = 'o produto nao foi editado ';
      header('location:admin.php');
   }

}

?>

<!DOCTYPE html>
<html lang="PT BR">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Painel de Cadastro</title>

   <!-- link das fontes  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- link do css  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php

if(isset($message)){
   foreach($message as $message){
      echo '<div class="message"><span>'.$message.'</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`;"></i> </div>';
   };
};

?>

<?php include 'header.php'; ?>

<div class="container">

<section>

<form action="" method="post" class="add-product-form" enctype="multipart/form-data">
   <h3>Adicionar Produto</h3>
   <input type="text" name="nome" placeholder="Digite o nome do Produto" class="box" required>
   <input type="number" name="sku" min="0" placeholder="Digite o SKU " class="box" required>
   <input type="text" name="descricao" min="0" placeholder="Digite a descricao" class="box" required>
   <input type="file" name="p_image" accept="image/png, image/jpg, image/jpeg" class="box" required>
   
   <h3>Variações</h3>
<input type="text" name="estoque" placeholder="Digite a quantidade no estoque" class="box" required>
<input type="number" name="preco" placeholder="preço do produto" class="box" required>
<input type="checkbox" name="tipo_de_variacao" value=""> Preto<br>
  <input type="checkbox" name="tipo_de_variacao" value=""> Tamanho M<br>
  <input type="checkbox" name="tipo_de_variacao" value=""> Polo<br>
  <input type="text" name="descricao_variacao" placeholder="Descrição da Variaçâo" class="box" required>
  <input type="submit" value="Adicionar Produto" name="add_produto" class="btn">
  <input type="text" name="descricao_variacao" placeholder= "Descrição da Variação" class="box" required>

</form>
</section>

<section class="display-product-table">

   <table>

      <thead>
         <th>Imagem do Produto</th>
         <th>Nome do Produto</th>
         <th>preço</th>
         <th>SKU</th>
         <th>Descrição</th>
         <th>Variaçâo</th>
         <th>Descrição da variação</th>
         <th>Ação</th>
      </thead>

      <tbody>
      <?php
         
         $select_products = mysqli_query($conn, "SELECT * FROM `produtos`");
         if(mysqli_num_rows($select_products) > 0){
            while($row = mysqli_fetch_assoc($select_products)){
      ?>
         

         <tr>
            <td><img src="uploaded_img/<?php echo $row['image']; ?>" height="100" alt=""></td>
            <td><?php echo $row['nome']; ?></td>
            <td>R$<?php echo $row['preco']; ?></td>
            <td><?php echo $row['sku']; ?></td>
            <td><?php echo $row['descricao']; ?></td>
            <td><?php echo $row['tipo_de_variacao']; ?></td>
            <td><?php echo $row['descricao_variacao']; ?></td>
          
            
            <td>
               <a href="admin.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Tem Certeza que deseja deletar?');"> <i class="fas fa-trash"></i> Apagar </a>
               <a href="admin.php?edit=<?php echo $row['id']; ?>" class="option-btn"> <i class="fas fa-edit"></i> Editar </a>
            </td>
         </tr>

         <?php
            };

            }else{
               echo "<div class='empty'>Nenhum Produto Cadastrado</div>";
            };
         ?>
      </tbody>
   </table>

</section>

<section class="edit-form-container">

   <?php
   
   if(isset($_GET['edit'])){
      $edit_id = $_GET['edit'];
      $edit_query = mysqli_query($conn, "SELECT * FROM `produtos` WHERE id = $edit_id");
      if(mysqli_num_rows($edit_query) > 0){
         while($fetch_edit = mysqli_fetch_assoc($edit_query)){
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <img src="uploaded_img/<?php echo $fetch_edit['image']; ?>" height="200" alt="">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
      <input type="text" class="box" required name="update_p_nome" placeholder="Digite o Nome" value="<?php echo $fetch_edit['nome']; ?>">
      <input type="number" min="0" class="box" required name="update_p_preco" placeholder= "Digite o preco" value="<?php echo $fetch_edit['preco']; ?>">
      <input type="file" class="box" required name="update_p_image" accept="image/png, image/jpg, image/jpeg">
      <input type="submit" value="Editar o Produto" name="update_produto" class="btn">
      <input type="reset" value="cancelar" id="close-edit" class="option-btn">
   </form>

   <?php
            };
         };
         echo "<script>document.querySelector('.edit-form-container').style.display = 'flex';</script>";
      };
   ?>

</section>

</div>















<!-- link do javascript-->
<script src="js/script.js"></script>

</body>
</html>