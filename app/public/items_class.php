<?php

class ItemTree {

   var $itemlist = array();


   function ItemTree($sql)
   {
      $result = mysqli_query($sql) or die("SQL: $sql <br>".mysqli_error());

      while ($row = mysqli_fetch_assoc($result)) {
         $this->itemlist[$row['id']] = array(
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

      return $item_tree;
   }



   function make_optionlist ($id, $class='', $delimiter='/')
   {
      $option_list = '';

      $item_tree = $this->get_tree(0);
   
      $options = $this->make_options($item_tree, '', $delimiter);

      if (!is_array($id)) {
         $id = array($id);
      }
   
      foreach($options as $row) {
         list($index, $text) = $row;
         $selected = in_array($index, $id) ? ' selected="selected"' : '';
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

      $search = array('{id}', '{name}');

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
      
      $search = array('{id}', '{name}'); 
   
      $output = "\n<ul class=\"$ul_class\">\n";
      
      foreach ($item_tree as $id => $item) {
         $replace = array($id, $item['name']);
         $output .= "<li class=\"$li_class\">".str_replace($search, $replace, $tpl);
         $output .= !empty($item['child']) ? "<br />".$this->get_node ($item['child'], $id, $tpl, $ul_class, $li_class) : '';
         $output .= "</li>\n";
      }
      
      return $output . "</ul>\n";
      
   }
   
   
   
   function get_id_in_node ($id)
   {
      $id_list = array($id);
      
      if (isset($this->itemlist[$id])) {
      
         foreach ($this->itemlist as $key => $row) {
            if ($row['parent'] == $id) {
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
   
   
   
   function get_parent ($id)
   {
      return isset($this->itemlist[$id]) ? $this->itemlist[$id]['parent'] : false;
   }
   
   
   
   function get_item_name ($id)
   {
      return isset($this->itemlist[$id]) ? $this->itemlist[$id]['name'] : false;
   }


}

?>