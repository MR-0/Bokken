<?php

foreach ($_POST as $key => $value) {
  $_POST[$key] = addslashes($value);
}