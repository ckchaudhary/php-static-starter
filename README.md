# php Simple Website Starter

A starter project for creating websites.


## Authors

- [@ckchaudhary](https://www.recycleb.in/u/chandan)


## Installation

- Clone this repository, or download the zip file and extract into root folder of your project.
- Open `config.php` and make necessary edits.
- Open `.htaccess` and make necessary edits.
- All done!
    
## Customization

Most of your edits should be inside `/public` folder. You can create templates for different pages of your website inside `/public/templates` folder. All custom php code( functions, classes, etc ) should ideally go inside `/public/inc` folder. All css, javascript, images etc should go inside `/public/assets` folder and so on.


## Features

#### Simple template strucutres

Create a file `public/templates/service.php` and that gets mapped to `yourdomain.com/service/` automatically. 

Create a file `public/templates/gallery/photos/album1.php` and that get mapped to `yourdomain.com/gallery/photos/album1/`. 

#### Many utility functions

Many functions for sanitization, validation, etc, are available. Most of these have been copied from WordPress core, with minimal modifications as and when needed. 

A working example contact form with jQueryForm, phpmailer etc.


#### TailWind CSS
It uses [tailwindcss](https://tailwindcss.com/) , mainly because I don't like writing CSS. You can use good old vanilla css or something else. 

If however, you wish to continue using tailwind:
- The file `public/assets/css/src/style.css` is the source file the output of which is written into `public/assets/css/style.css`
- To compile the source and generate output, browse to the root folder of the project on terminal and run the following:
```
npx tailwindcss -i ./public/assets/css/src/style.css -o ./public/assets/css/style.css --minify  
```