<?php

class ItemTree {

	var $itemlist = array();

   function ItemTree($sql)
   {
      $result = mysqli_query($sql) or die("SQL: $sql <br>".mysqli_error());

      while ($row = mysqli_fetch_assoc($result)) {
         $this->itemlist[$row['catid']] = array(
           'name'   => $row['name'],
           'parent' => $row['parent']
         );
      }

   }



   function get_tree($parent, $with_parent=0)
   {
      $item_tree = array();
   
      if ($with_parent == 1 && $parent != 0) {
         $item_tree[$parent]['name'] = $this->itemlist[$parent]['name'];
         $item_tree[$parent]['parent'] = $this->itemlist[$parent]['parent'];
         $item_tree[$parent]['child'] = $this->get_tree($parent);
      
         return $item_tree;
      }

      foreach ($this->itemlist as $key => $val) {
         if ($val['parent'] == $parent) {
               $item_tree[$key]['name'] = $val['name'];
               $item_tree[$key]['parent'] = $val['parent'];
               $item_tree[$key]['child'] = $this->get_tree($key);
         }
      }

	  if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		print_r($item_tree);
	  } else {
		return $item_tree;
	  }
   }



   function make_optionlist ($catid, $class='', $delimiter='/')
   {
      $option_list = '';

      $item_tree = $this->get_tree(0);
   
      $options = $this->make_options($item_tree, '', $delimiter);

      if (!is_array($catid)) {
         $catid = array($catid);
      }
   
      foreach($options as $row) {
         list($index, $text) = $row;
         $selected = in_array($index, $catid) ? ' selected="selected"' : '';
         $option_list .= "<option value=\"$index\" class=\"$class\"$selected>$text</option>\n";
      }
   
      return $option_list;
   }



   function make_options ($item_tree, $before, $delimiter='/')
   {
      $before .= empty($before) ? '' : $delimiter;

      $options = array();
   
      foreach ($item_tree as $key => $val) {
         $options[] = array($key, $before.$val['name']);
         if (!empty($val['child'])) {
            $options = array_merge($options, $this->make_options($val['child'], $before.$val['name'], $delimiter));
         }
      }

      return $options;
   }



   function get_navlinks ($navid, $tpl, $startlink='', $delimiter=' &raquo; ')
   {
      // $tpl typ: <a href="index.php?id={id}" class="navlink">{name}</a>

      $search = array('{catid}', '{name}');

      $navlink = array();

      while (isset($this->itemlist[$navid])) {
         $replace = array($navid, $this->itemlist[$navid]['name']);
         $navlink[] = str_replace($search, $replace, $tpl);
         $navid = $this->itemlist[$navid]['parent'];
      }

      if (!empty($startlink)) {
         $navlink[] = str_replace($search, array(0, $startlink), $tpl);
      }
   
      $navlink = array_reverse($navlink);
   
      return implode($delimiter, $navlink);
   }
   
   
   
   function show_tree ($parent=0, $tpl='%s', $ul_class='', $li_class='')
   {

      $item_tree = $this->get_tree($parent);
      
      return $this->get_node($item_tree, $parent, $tpl, $ul_class, $li_class);
      
      
   }
   
   
   
   function get_node ($item_tree, $parent, $tpl, $ul_class, $li_class)
   {
      // $tpl typ: <a href="item.php?id={id}" class="treelink" style="color:blue">{name}</a>
      
      $search = array('{catid}', '{name}'); 
   
      $output = "\n<ul class=\"$ul_class\">\n";
      
      foreach ($item_tree as $catid => $item) {
         $replace = array($catid, $item['name'] . " (" . $catid . ")");
         $output .= "<li class=\"$li_class\">".str_replace($search, $replace, $tpl);
         $output .= !empty($item['child']) ? $this->getIfAnyProductsInCategoryParent($catid)."<br />".$this->get_node ($item['child'], $catid, $tpl, $ul_class, $li_class) : '';
         $output .= empty($item['child']) ? $this->getIfAnyProductsInCategory($catid) : '';
         $output .= "</li>\n";
      }
      
      return $output . "</ul>\n";
      
   }
   
   
   
   function get_id_in_node ($catid)
   {
      $id_list = array($catid);
      
      if (isset($this->itemlist[$catid])) {
      
         foreach ($this->itemlist as $key => $row) {
            if ($row['parent'] == $catid) {
               if (!empty($row['child'])) {
                 $id_list = array_merge($id_list, get_id_in_node($key));
               } else {
                 $id_list[] = $key;
               }
            }
         }
      
      }
      
      return $id_list;
   }
   
   
   
   function get_parent ($catid)
   {
      return isset($this->itemlist[$catid]) ? $this->itemlist[$catid]['parent'] : false;
   }
   
   
   
   function get_item_name ($catid)
   {
      return isset($this->itemlist[$catid]) ? $this->itemlist[$catid]['name'] : false;
   }
   
	function getIfAnyProductsInCategory($catid) {
		
		$conn_my = Db::getConnection();

		$select  = "SELECT art.artnr ";
		$select .= "FROM Artiklar art ";
		$select .= "WHERE art.ej_med=0 AND (art.demo=0 OR art.lagersaldo > 0) AND (art.utgangen=0 OR art.lagersaldo > 0) ";
		$select .= "AND art.kategori_id = '$catid' ";
		// $select .= "AND NOT art.demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
				return '<img border=0 src=check.png>';
			} else {
				return '<img border=0 src=not_check.png>';
			}
		
	}

	function getIfAnyProductsInCategoryParent($catid) {
		
		$conn_my = Db::getConnection();

		$select  = "SELECT art.artnr ";
		$select .= "FROM Artiklar art ";
		$select .= "WHERE art.ej_med=0 AND (art.demo=0 OR art.lagersaldo > 0) AND (art.utgangen=0 OR art.lagersaldo > 0) ";
		$select .= "AND art.kategori_id = '$catid' ";
		// $select .= "AND NOT art.demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
				return '<img border=0 src=not_check.png>';
			} else {
				return '<img border=0 src=check.png>';
			}
		
	}
	
	function showProductsInCategory($catid) {
		
		$conn_my = Db::getConnection();

		$select  = "SELECT art.artnr, art.beskrivning, tillv.Tillverkare ";
		$select .= "FROM Artiklar art ";
		$select .= "JOIN Tillverkare tillv ON tillv.tillverkar_id = art.tillverkar_id ";
		$select .= "WHERE art.ej_med=0 AND (art.demo=0 OR art.lagersaldo > 0) AND (art.utgangen=0 OR art.lagersaldo > 0) ";
		$select .= "AND art.kategori_id = '$catid' ";
		// $select .= "AND NOT art.demo=-1 " ; // initialt visar vi inga demoprodukter på mobilsidan
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
			exit;
		}
		
		$res = mysqli_query($conn_my, $select);
		$num_rows = mysqli_num_rows($res);
			
			if (mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_array($res)) {
				extract($row);

				if ($Tillverkare == ".") {
					$Tillverkare = "";
				}
				
				$output .= "- <a target=\"_blank\" class=\"item_title\" href=\"/info.php?article=$artnr\">";
				$output .= "$Tillverkare $beskrivning</a><br>\n";
				
				}
			} else {
				$output .= "<b>Inga aktiva produkter finns i denna kategori</b>\n";
			}
		
		return $output . "\n";
	}
	
}

?>