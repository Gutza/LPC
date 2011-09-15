<?php

  class LPC_Scaffold
  {

    // If empty, all classes are shown. If specified, only those classes are shown.
    var $expose_classes=array();

    function __construct()
    {
    }

    function show()
    {
      $p=new LPC_Page_scaffolding();
      LPC_Page::setCurrent($p);
      if (!LPC_GUI_OB) {
        ob_start();
      }
      $this->expose();
      $p->content=ob_get_clean();
      $p->show();
      $p->renderMode='none';
    }

    function expose()
    {
      if (!isset($_GET['action'])) {
        return $this->exposeClasses();
      }
      switch($_GET['action']) {
        case 'class':
          return $this->exposeClass($_GET['class']);
        case 'new':
          return $this->guiNew();
        case 'edit':
          return $this->guiOld();
        case 'save':
          return $this->saveObject();
        case 'delete':
          return $this->deleteObject();
        default:
          return $this->showError("Unknown action {".htmlspecialchars($_GET['action'])."}");
      }
    }

    function deleteObject()
    {
      if (!$class=$this->getEditClass()) {
        return false;
      }
      $o=new $class((int) $_GET['id']);
      $o->delete();
      header("Location: ?action=class&class=".get_class($o));
    }

    function saveObject()
    {
      if (!$class=$this->getEditClass()) {
        return false;
      }
      $o=new $class((int) $_GET['id']);
      $attrs=$o->dataStructure['fields'];
      $ok=true;
      $validation_messages=array();
      foreach($attrs as $name=>$props) {
        if (!isset($_POST[$name])) {
          $ok=false;
          break;
        }
        if (
          isset($o->dataStructure['fields'][$name]['base_type']) &&
          $o->dataStructure['fields'][$name]['base_type']=='date'
        ) {
          $o->setAttr($name,strtotime($_POST[$name]));
        } elseif (
          isset($o->dataStructure['fields'][$name]['base_type']) &&
          $o->dataStructure['fields'][$name]['base_type']=='datetime'
        ) {
          $o->setAttr($name,strtotime(implode(' ',$_POST[$name])));
        } else {
          $o->setAttr($name,$_POST[$name]);
        }
        if ($errs=$o->validate($name)) {
          $validation_messages=array_merge($validation_messages,$errs);
        }
      }
      if (!$ok) {
        $this->showError("Error saving your object!");
        return $this->guiEditCommon($o);
      }
      if ($validation_messages) {
        $this->showError("There were some validation problems:<ul><li>".implode("<li>",$validation_messages)."</ul>");
        return $this->guiEditCommon($o);
      }
      $o->save();
      header("Location: ?action=class&class=".$class);
    }

    function getEditClass()
    {
      $class=$_GET['class'];
      if (!$this->validClassName($class)) {
        $this->showError("Invalid class name {".htmlspecialchars($class)."}");
        return false;
      }
      return $class;
    }

    function guiNew()
    {
      if (!$class=$this->getEditClass()) {
        return false;
      }
      $o=new $class();
      return $this->guiEditCommon($o);
    }

    function guiOld()
    {
      if (!$class=$this->getEditClass()) {
        return false;
      }
      $o=new $class((int) $_GET['id']);
      if (!$o->probe()) {
        return $this->showError("This object doesn't exist!");
      }
      return $this->guiEditCommon($o);
    }

    function guiEditCommon($object)
    {
      echo "[<a href='?'>&larr;&larr; Choose class</a>]\n";
      echo "[<a href='?action=class&amp;class=".get_class($object)."'>&larr; Choose object</a>]\n";
      echo "<form method='POST' action='?action=save&amp;class=".get_class($object)."&amp;id=".((int) $object->id)."'>\n";
      echo "<table class='default'>\n";
      echo "<tr>\n";
      echo "<th>Field</th>\n";
      echo "<th>Value</th>\n";
      echo "</tr>\n";
      $attrs=$object->dataStructure['fields'];
      foreach($attrs as $name=>$props) {
        $nameH=$inputName=htmlspecialchars($name);
        echo "<tr>";
        echo "<th>".$object->getAttrNameH($name);
        if ($props['flags']['required']) {
          echo "&nbsp;<span style='color:red'><b>*</b></span>";
        }
        if ($desc=$object->getAttrDescH($name)) {
          echo "<div class='th-explain'>$desc</div>";
        }
        echo "</th>\n";
        echo "<td>";
        if (isset($props['base_type'])) {
          $display_type=$props['base_type'];
        } else {
          $display_type="string";
        }
        echo "<div class='scaffold-type'>[".$display_type."]</div>";
        echo "<input type='text' id=\"".$nameH."\" ";
        if (isset($props['base_type']) && in_array($props['base_type'],array('date','datetime'))) {
          echo "class='date_input' ";
          if ($props['base_type']=='datetime') {
            $inputName=$nameH."[0]";
          }
        } else {
          echo "value=\"".$object->getAttrH($name)."\" ";
        }
        echo "name=\"".$inputName."\">\n";
        if (isset($props['base_type']) && in_array($props['base_type'],array('date','datetime'))) {
          if ($object->getAttr($name)) {
            $date=date('Y-m-d',$object->getAttr($name));
          } elseif ($props['flags']['required']) {
            $date=date('Y-m-d');
          } else {
            $date='';
          }
          echo "<script type='text/javascript'>$(function() { $('#$nameH').datepicker('setDate','$date'); ";
          echo "});</script>";
        }
        if ($props['base_type']=='datetime') {
          if ($object->getAttr($name)) {
            $time=date('H:i:s',$object->getAttr($name));
          } elseif ($props['flags']['required']) {
            $time=date('H:i:s');
          } else {
            $time='';
          }
          echo " <input type='text' name=\"{$nameH}[1]\" value='$time'>";
        }
        echo "</td>\n";
        echo "</tr>\n";
      }
      echo "<tr><th>&nbsp;</th>\n";
      $label=$object->id?'Edit':'Create';
      echo "<td><input type='submit' name='submit' value='".$label."'></td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "</form>\n";
      //echo "<pre>\n"; var_dump($attrs); echo "</pre>\n";
    }

    function exposeClass($class)
    {
      $classH=htmlspecialchars($class);
      if (!$this->validClassName($class)) {
        return $this->showError("Invalid class name {$classH}");
      }
      $o=new $class();
      echo "[<a href='?'>&larr; Choose class</a>]\n";
      echo "[<a href='?action=new&amp;class=$classH'>Create item</a>]\n";
      $list=$o->search(NULL,NULL,0);
      if ($list) {
        echo "<ul>\n";
      }
      foreach($list as $item) {
        echo "<li>";
        echo "<a href='?action=edit&amp;class=$classH&amp;id=".$item->id."'>$classH#".$item->id."</a> ";
        echo " [<a href='?action=delete&amp;class=$classH&amp;id=".$item->id."' onClick='return confirm(\"You sure?\");'>delete</a>]";
        echo "</li>\n";
      }
      if ($list) {
        echo "</ul>\n";
      }
    }

    function exposeClasses()
    {
      echo "<div>Available classes:</div>";
      echo "<ul>";
      $this->exposeDirClasses(LPC_classes);
      global $LPC_extra_class_dirs;
      foreach($LPC_extra_class_dirs as $dir) {
        $this->exposeDirClasses($dir);
      }
      echo "</ul>";
    }

    function exposeDirClasses($dir)
    {
      $d = dir($dir);
      while (false !== ($entry = $d->read())) {
        $fname=LPC_classes."/".$entry;
        $class=substr($entry,0,-4);
        if (!$this->validClassFile($fname) || !$this->validClassName($class)) {
          continue;
        }
        echo "<li><a href='?action=class&amp;class=".$class."'>$class</a></li>\n";
      }
      $d->close();
    }

    function validClassFile($fname)
    {
      if (is_dir($fname)) return false;
      if (substr($fname,-4)!='.php') return false;
      return true;
    }

    function validClassName($class)
    {
      if (substr($class,0,4)=='LPC_') {
        return false;
      }
      if (!class_exists($class)) {
        return false;
      }
      if (get_parent_class($class)=='LPC_Object') {
        return false;
      }
      if (!empty($this->expose_classes)) {
        if (!in_array($class,$this->expose_classes)) {
          return false;
        }
      }
      return true;
    }

    function showError($error)
    {
      echo "<div style='color:red'>ERROR: ".$error."</div>\n";
    }
  }
