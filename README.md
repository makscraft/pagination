# Pagination
Pagination manager for Symfony applications.

Generates list of links and parameters for SQL queries.

![pagination](https://github.com/user-attachments/assets/620c1f38-3b7d-4764-b186-38c5e2031e7c)


Installation
---

composer require makscraft/pagination

Example of use in controller or repository
---

```
use Makscraft\Pagination;

$total = ... ; //count your all needed items
$limit = 20; //items per page

$pagination = new Pagination($total, $limit);
$repository -> findBy([...], [...], $pagination -> getLimit(), $pagination -> getOffset());
  
//Returns array of links for html template.
$data = $pagination -> setPath('/catalog') -> getDisplayData();
```
Result links in $data will look like: '/catalog?page=3'.

You can pass the path with additional GET parameters if you want.

```
$pagination -> setPath('/catalog?sort=name&direction=asc');
```

For twig templates
---

Use package twig template or copy it into templates directory of your project.
Inside the controller pass the pagination object into template.
```
return $this -> render('mypage.html.twig', [
    
    ...
    
    'pagination' => $pagination
]);
```
To use template from vendor directory add the path into twig config file.
```
# config/packages/twig.yaml    
paths:
    'vendor/makscraft/pagination/templates': 'pagination'

```
Next include the pagination template into your twig files.
```
{% include '@pagination/pagination.html.twig' %}
```

Also you can copy the template file into your templates directory and use it as you like.

Template file location: **vendor/makscraft/pagination/templates/pagination.html.twig**
