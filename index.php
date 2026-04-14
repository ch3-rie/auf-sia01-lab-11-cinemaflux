<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIA Lab 11 | CINEMAFLUX</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.5.1/themes/reset-min.css">
  
  <style>
    :root {
      --primary: #38bdf8;
      --bg: #030712;
      --surface: rgba(17, 24, 39, 0.8);
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --border: rgba(255, 255, 255, 0.08);
      --card-height: 540px;
      --poster-height: 320px;
    }

    * { box-sizing: border-box; -webkit-font-smoothing: antialiased; }

    body {
      margin: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--text-main);
      background-color: var(--bg);
      background-image: radial-gradient(circle at 50% -10%, rgba(56, 189, 248, 0.1), transparent 50%);
      min-height: 100vh;
    }

    .page { max-width: 1400px; margin: 0 auto; padding: 40px 24px; }

    header { text-align: center; margin-bottom: 30px; }
    h1 { font-weight: 800; font-size: 2.2rem; letter-spacing: -0.04em; margin: 0; color: #fff; }
    .tagline { font-size: 0.9rem; color: var(--text-muted); margin-top: 8px; font-weight: 500; }

    /* Search Area */
    .search-area { max-width: 600px; margin: 40px auto 30px auto; position: relative; }
    .ais-SearchBox-form {
      position: relative;
      background: var(--surface) !important;
      border-radius: 16px;
      border: 1px solid var(--border);
    }
    .ais-SearchBox-submit, .ais-SearchBox-reset { display: none !important; }
    .ais-SearchBox-input {
      width: 100%;
      background: transparent !important;
      border: none !important;
      color: white !important;
      padding: 16px 20px 16px 54px !important;
      font-size: 0.95rem;
    }
    .search-area::before {
      content: ""; position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
      width: 18px; height: 18px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2338bdf8' stroke-width='2.5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'/%3E%3C/svg%3E");
      background-size: contain; background-repeat: no-repeat; z-index: 5;
    }

    /* Filters Ribbon (Genres + Year) */
    .filter-ribbon { 
      display: flex; 
      flex-direction: column; 
      align-items: center; 
      gap: 20px; 
      margin-bottom: 40px; 
    }

    /* Genre Pills */
    .ais-RefinementList-list { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; list-style: none; }
    .ais-RefinementList-item { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 6px 14px; cursor: pointer; transition: 0.2s; }
    .ais-RefinementList-item--selected { background: var(--primary) !important; border-color: var(--primary); }
    .ais-RefinementList-labelText { color: var(--text-muted); font-size: 0.75rem; font-weight: 700; cursor: pointer; }
    .ais-RefinementList-item--selected .ais-RefinementList-labelText { color: var(--bg); }
    .ais-RefinementList-count, .ais-RefinementList-checkbox { display: none; }

    /* Year Dropdown Styling */
    .year-filter { min-width: 200px; }
    .ais-Menu-select {
      width: 100%;
      background: var(--surface);
      color: var(--text-main);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 10px;
      font-family: inherit;
      outline: none;
      cursor: pointer;
    }

    /* Precision Card Grid */
    .ais-Hits-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 24px;
    }

    .movie-card {
      background: rgba(255,255,255,0.02);
      border-radius: 16px;
      border: 1px solid var(--border);
      height: var(--card-height);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .movie-card:hover {
      background: rgba(255,255,255,0.04);
      border-color: rgba(56, 189, 248, 0.4);
      transform: translateY(-4px);
    }

    .poster-wrap {
      width: 100%;
      height: var(--poster-height);
      flex-shrink: 0;
      overflow: hidden;
      background: #111;
    }

    .poster-wrap img { width: 100%; height: 100%; object-fit: cover; }

    .movie-body { padding: 18px; display: flex; flex-direction: column; flex-grow: 1; }

    .movie-title {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 10px;
      color: #fff;
      line-height: 1.3;
      height: 2.6em; 
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .meta-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    
    .rating-pill {
      background: rgba(56, 189, 248, 0.1);
      color: var(--primary);
      font-size: 0.7rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 6px;
    }

    .year-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }

    .overview {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
      height: 4.5em; 
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-top: auto;
    }

    .ais-Pagination-list { margin-top: 50px; justify-content: center; gap: 8px; }
    .ais-Pagination-link { background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 10px 16px; color: white; transition: 0.2s; }
    .ais-Pagination-item--selected .ais-Pagination-link { background: var(--primary); color: var(--bg); font-weight: 800; border-color: var(--primary); }

  </style>
</head>
<body>
  <main class="page">
    <header>
      <h1>CINEMAFLUX</h1>
      <p class="tagline">Helping you choose a movie faster than you’ll actually watch it.</p>
    </header>

    <div class="search-area">
      <div id="searchbox"></div>
    </div>

    <div class="filter-ribbon">
      <div id="genre-list"></div>
      <div class="year-filter">
        <div id="year-menu"></div>
      </div>
    </div>

    <section>
      <div id="hits"></div>
      <div id="pagination"></div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/algoliasearch@4/dist/algoliasearch-lite.umd.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/instantsearch.js@4"></script>
  
  <script>
    const searchClient = algoliasearch('NAVX0XCPG4', '68df21dae8143843307be5b1fbc86f29');
    const search = instantsearch({ indexName: 'movies', searchClient });

    const { searchBox, hits, refinementList, pagination, menu } = instantsearch.widgets;

    search.addWidgets([
      searchBox({ container: '#searchbox', placeholder: 'Search movies...' }),
      
      refinementList({ container: '#genre-list', attribute: 'genres', limit: 12 }),
      
      menu({ 
        container: '#year-menu', 
        attribute: 'year_released',
        templates: {
          defaultOption: 'All Release Years'
        }
      }),

      hits({
        container: '#hits',
        templates: {
          item(hit) {
            const poster = hit.poster || 'https://via.placeholder.com/400x600?text=Cinema';
            const rating = hit.rating ? hit.rating.toFixed(1) : '0.0';
            const year = hit.year_released || 'N/A';
            return `
              <article class="movie-card">
                <div class="poster-wrap">
                  <img src="${poster}" alt="${hit.title}" onerror="this.src='https://via.placeholder.com/400x600?text=Cinematic'" />
                </div>
                <div class="movie-body">
                  <h3 class="movie-title">${instantsearch.highlight({ attribute: 'title', hit })}</h3>
                  <div class="meta-row">
                    <div class="rating-pill">★ ${rating}</div>
                    <span class="year-label">${year}</span>
                  </div>
                  <p class="overview">${hit.overview || 'No synopsis available for this title.'}</p>
                </div>
              </article>
            `;
          },
        }
      }),
      pagination({ container: '#pagination' })
    ]);

    search.start();
  </script>
</body>
</html>