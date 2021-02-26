# change wd
setwd(rstudioapi::getActiveProject())
setwd("resources/book")


# render a chapter or the whole book
browseURL(bookdown::render_book("index.Rmd"))

browseURL(bookdown::preview_chapter("app-2_news.Rmd"))
