---
Title: How to make a small wiki
---
Learn how to make your own wiki.

## Installing wiki

1. [Download and unzip Datenstrom Yellow](https://github.com/datenstrom/yellow/archive/master.zip).
2. Copy all files to your web server.
3. Open your website in a web browser and select 'Wiki'.

Your wiki is immediately available. The installation comes with two pages, 'Home' and 'Wiki'. This is just an example to get you started, change everything as you like. You can delete the home page, if you want to show the wiki on the home page.

When there are problems with installation, see [troubleshooting](troubleshooting).

## Writing wiki pages

Have a look inside your `content` folder, there's the wiki folder with all your wiki pages. Open the file `wiki-page.md`. You'll see settings and text of the page. You can change `Title` and other [settings](markdown-cheat-sheet#settings) at the top of a page. Below that you can use [Markdown](markdown-cheat-sheet). Here's an example:

```
---
Title: Wiki page
Layout: wiki
Tag: Example
---
This is an example wiki page. 

Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod 
tempor incididunt ut labore et dolore magna pizza. Ut enim ad minim veniam, 
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo. 
```

To create a new wiki page, add a new file to the wiki folder. Set `Title` and more settings at the top of a page. You can use `Tag` to group similar pages together. Here's another example:

```
---
Title: Coffee is good for you
Layout: wiki
Tag: Example, Coffee
---
Coffee is a beverage made from the roasted beans of the coffee plant.

1. Start with fresh coffee. Coffee beans start losing quality immediately 
   after roasting and grinding. The best coffee is made from beans ground 
   right after roasting. 
2. Brew a cup of coffee. Coffee is prepared with different methods and 
   additional flavorings such as milk and sugar. There are Espresso, Filter 
   coffee, French press, Italian Moka, Turkish coffee and many more. Find 
   out what's your favorite.
3. Enjoy.
```

Now let's add a video with the [Youtube extension](https://github.com/datenstrom/yellow-extensions/tree/master/source/youtube):

```
---
Title: Coffee is good for you
Layout: wiki
Tag: Example, Coffee, Video
---
Coffee is a beverage made from the roasted beans of the coffee plant.

1. Start with fresh coffee. Coffee beans start losing quality immediately 
   after roasting and grinding. The best coffee is made from beans ground 
   right after roasting. 
2. Brew a cup of coffee. Coffee is prepared with different methods and 
   additional flavorings such as milk and sugar. There are Espresso, Filter 
   coffee, French press, Italian Moka, Turkish coffee and many more. Find 
   out what's your favorite.
3. Enjoy.

[youtube SUpY1BT9Xf4]
```

You can use [shortcuts](https://github.com/datenstrom/yellow-extensions/tree/master/source/wiki#how-to-show-wiki-information) to show information about the wiki.

## Showing header

To show a header create the file `content/shared/header.md`. Here's an example:

```
---
Title: Header
Status: shared
---
I like Markdown.
```

## Showing footer

To show a footer create the file `content/shared/footer.md`. Here's an example:

```
---
Title: Footer
Status: shared
---
[Made with Datenstrom Yellow](https://datenstrom.se/yellow/)
```

## Get extensions

There are [extensions](https://github.com/datenstrom/yellow-extensions) for your wiki. Here are some examples:

* [How to make a table of contents](https://github.com/datenstrom/yellow-extensions/tree/master/source/toc)
* [How to use a search](https://github.com/datenstrom/yellow-extensions/tree/master/source/search)
* [How to use a contact page](https://github.com/datenstrom/yellow-extensions/tree/master/source/contact)
* [How to make a draft page](https://github.com/datenstrom/yellow-extensions/tree/master/source/draft)
* [How to build a static website](https://github.com/datenstrom/yellow-extensions/tree/master/source/command)
