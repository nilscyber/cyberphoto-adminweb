<?php
 
include 'conn.php'; // Databasanslutningen
include 'items_class.php'; // Kategori-klass
 
 
if (isset($_POST['save_item'])) {
   save_item();
} elseif (isset($_GET['edit'])) {
   edit_item();
} elseif (isset($_GET['delete'])) {
   delete_item();
} elseif (isset($_GET['new'])) {
   make_form();
} else {
   list_items();
}
 
 
 
function edit_item ()
{
   $sql = "SELECT id, item, parent FROM items WHERE id = ".intval($_GET['edit']);
   $result = mysqli_query($sql);
   
   if (mysqli_num_rows($result) == 0) {
      header("Location: item.php");
      exit;
   }
   
   extract(mysqli_fetch_assoc($result));

   make_form($id, $item, $parent);
}
 
 
 
function save_item ()
{
   if (isset($_POST['delete_item'])) {
      header("Location: item.php?delete=".$_POST['delete_item']);
      exit;
   }

   if (empty($_POST['id'])) {
      $sql = "INSERT INTO items (item, parent) VALUES ('{$_POST['item']}', '{$_POST['parent']}')";
   } else {
      $sql = "UPDATE items SET item = '{$_POST['item']}', parent = '{$_POST['parent']}' WHERE id = ".intval($_POST['id']);
   }
 
   mysqli_query($sql);
   
   header("Location: item.php");
   exit;
}
 
 
 
function delete_item ()
{
   $sql = "SELECT id AS id, item AS name, parent AS parent FROM items ORDER BY name";
   $items = new ItemTree($sql);
   
   if ($items->get_item_name($_GET['delete']) !== false ) {
      $id_list = $items->get_id_in_node($_GET['delete']);
      $id_list = implode(", ", $id_list);
   } else {
      $id_list = 0;
   }

   $sql = "DELETE FROM items WHERE id IN ($id_list)";
   mysqli_query($sql) or die(mysqli_error());
   
   header("Location: item.php");
   exit;
}



function make_form ($id=0, $item='', $parent=0)
{
   $sql = "
     SELECT id AS id, item AS name, parent AS parent 
     FROM items 
     WHERE id <> ".intval($id)."
     ORDER BY name
   ";
   $items = new ItemTree($sql);
   $options = $items->make_optionlist($parent);
   
   $headline = empty($id) ? 'Ny' : 'Ändra';
   
   include 'top.tpl.php';
 
   echo '
     <h1>'.$headline.' kategori</h1>
     
     <form action="item.php" method="post" onsubmit="return this.delete_item.checked ? confirm(\'Radera denna kategori, med underkategorier ?\') : true">
     
     <input type="hidden" name="id" value="'.$id.'" />
 
     <p>Ligger under:<br />
     <select name="parent">
     <option value="0">Huvudkategori</option>
     '.$options.'
     </select>
     </p>
 
     <p>Kategori:<br />
     <input type="text" name="item" value="'.htmlentities($item).'" /></p>
   ';

   if (!empty($id)) {
   
      echo '
        <p><input type="checkbox" name="delete_item" value="'.$id.'" id="delete" />
        <label for="delete">Radera</label></p>
      ';

   }

   echo '
     <p><input type="submit" name="save_item" value="Spara" style="margin-left:10px; width:80px" />
        <input type="button" value="Avbryt" onclick="history.go(-1)" style="margin-left:10px; width:80px" />
     </p>
 
     </form>
   ';
   
   include 'bottom.tpl.php';
 
}
 
 
 
function list_items ()
{
   // $sql = "SELECT id AS id, item AS name, parent AS parent FROM items ORDER BY name";
   $sql = "SELECT kategori_id AS id, kategori AS name, kategori_id_parent AS parent FROM Kategori WHERE visas = -1 ORDER BY name";
   $items = new ItemTree($sql);

   $id = isset($_GET['id']) && intval($_GET['id']) > 0 ? $_GET['id'] : 0;

   $tpl_nav = '<a href="item.php?id={id}" class="navlink">{name}</a>';
   $startlink = '<a href="item.php" class="navlink" style="color:navy">Start</a>';
   $nav_links = $items->get_navlinks($id, $tpl_nav, $startlink);

   $tpl_items = '&bull; <a href="item.php?id={id}" class="treelink" style="color:blue">{name}</a>';
   $tree = $items->show_tree($id, $tpl_items, 'tree');
   
   $info = !empty($id) ? '<div style="padding:10px">Visar information om '.$items->get_item_name($id).'</div>' : '';
   
   
   $tpl_items = '&bull; <a href="item.php?edit={id}" class="treelink" style="color:blue">{name}</a>';
   $admin = $items->show_tree(0, $tpl_items, 'tree');
   
   
   include 'top.tpl.php';

   echo '
     <div class="rubrik">Visa kategorier</div>
     
     <div style="background-color:#CCCCCC; padding:3px 15px; margin-bottom:15px; margin-top:15px;">
     '.$nav_links.'
     </div>
     
     '.$info.'
     
     '.$tree.'
   ';

   /*
   echo '
     <h1>Visa kategorier</h1>
     
     <div style="background-color:#CCCCCC; padding:3px 15px; margin-bottom:5px;">
     '.$nav_links.'
     </div>
     
     '.$info.'
     
     '.$tree.'
     
     <hr>
     <h1>Administration kategorier</h1>
     
     '.$admin.'

     <p><a href="item.php?new=" class="treelink" style="color:blue">Lägg till ny kategori</a></p>
   ';
   */
   
   include 'bottom.tpl.php';
}
 
?>