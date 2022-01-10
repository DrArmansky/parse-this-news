DROP TABLE IF EXISTS public.ptn_settings,
public.ptn_news;
CREATE TABLE public.ptn_settings (
  source TEXT PRIMARY KEY,
  link_selector TEXT NOT NULL,
  title_selector TEXT NOT NULL,
  text_selector TEXT NOT NULL,
  image_selector TEXT NOT NULL,
  news_limit INTEGER NULL
);
CREATE TABLE public.ptn_news (
  id serial PRIMARY KEY,
  code TEXT NOT NULL,
  source TEXT NOT NULL,
  title TEXT NOT NULL,
  text TEXT NULL,
  image TEXT NULL,
  FOREIGN KEY (source) REFERENCES public.ptn_settings (source)
);