<?php

namespace App\Enums;

enum FileFolderEnum: string
{
    case DisplayPicture = "displayPictures";
    case AdminDisplayPicture = "displayPictures/admin";
    case Documents = "documents";
    case SKID = "skid";
    case Products = "products";
    case Posts = "posts";
}