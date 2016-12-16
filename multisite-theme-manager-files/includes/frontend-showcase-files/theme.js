/* global _wpThemeSettings, confirm */
window.wp = window.wp || {};

( function($) {

// Set up our namespace...
var themes, l10n;
themes = wp.themes = wp.themes || {};

// Store the theme data and settings for organized and quick access
// themes.data.settings, themes.data.themes, themes.data.l10n
themes.data = _wpThemeSettings;
l10n = themes.data.l10n;
categories = themes.data.categories;

// Setup app structure
_.extend( themes, { model: {}, view: {}, routes: {}, router: {}, template: wp.template });

themes.model = Backbone.Model.extend({});

// Main view controller for themes.php
// Unifies and renders all available views
themes.view.Appearance = wp.Backbone.View.extend({

  el: '.wmd-themes-showcase',

  window: $( window ),
  // Pagination instance
  page: 0,

  // Sets up a throttler for binding to 'scroll'
  initialize: function() {
    // Scroller checks how far the scroll position is
    _.bindAll( this, 'scroller' );

    // Bind to the scroll event and throttle
    // the results from this.scroller
    this.window.bind( 'scroll', _.throttle( this.scroller, 300 ) );
  },

  // Main render control
  render: function() {
    // Setup the main theme view
    // with the current theme collection
    this.view = new themes.view.Themes({
      collection: this.collection,
      parent: this
    });

    // Render categories form.
    this.categories();

    // Render search form.
    this.search();

    // Render and append
    this.view.render();
    this.$el.empty().append( this.view.el ).addClass('rendered');
    this.$el.append( '<br class="clear"/>' );
  },

  // Search input and view
  // for current theme collection
  categories: function() {
    var view,
      self = this;

    // Don't render the categories if there is only one theme or one category(all)
    var count_categories = 0;
    var i;

    for (i in categories) {
        if (categories.hasOwnProperty(i)) {
            count_categories++;
        }
    }
    if ( themes.data.themes.length === 1 || count_categories < 2 || $('.wmd-themes-showcase.hide-interface').length ) {
      return;
    }

    view = new themes.view.Categories({ collection: self.collection });

    // Render and append before themes list
    view.render();
    $('.wmd-themes-showcase')
      .before( view.el );
  },

  // Search input and view
  // for current theme collection
  search: function() {
    var view,
      self = this;

    // Don't render the search if there is only one theme
    if ( themes.data.themes.length === 1 || $('.wmd-themes-showcase.hide-interface').length ) {
      return;
    }

    view = new themes.view.Search({ collection: self.collection });

    // Render and append after screen title
    view.render();
    $('.theme-categories')
      .append( $.parseHTML( '<div class="theme-search-holder"></div>' ) );

    $('.theme-search-holder')
      .append( $.parseHTML( '<label class="screen-reader-text" for="theme-search-input">' + l10n.searchFE + '</label>' ) )
      .append( view.el );
  },

  // Checks when the user gets close to the bottom
  // of the mage and triggers a theme:scroll event
  scroller: function() {
    var self = this,
      bottom, threshold;

    bottom = this.window.scrollTop() + self.window.height();
    threshold = self.$el.offset().top + self.$el.outerHeight( false ) - self.window.height();
    threshold = Math.round( threshold * 0.98 );

    if ( bottom > threshold ) {
      this.trigger( 'theme:scroll' );
    }
  }
});

// Set up the Collection for our theme data
// @has 'id' 'name' 'screenshot' 'author' 'authorURI' 'version' 'active' ...
themes.Collection = Backbone.Collection.extend({

  model: themes.model,

  // Search terms
  terms: '',

  // Controls searching on the current theme collection
  // and triggers an update event
  doSearch: function( value ) {
    //reset category to "all" on search
    $('.theme-categories .theme-category').removeClass('current');
    $('[data-category="all"]').addClass('current');

    // Don't do anything if we've already done this search
    // Useful because the Search handler fires multiple times per keystroke
    if ( this.terms === value ) {
      return;
    }

    // Updates terms with the value passed
    this.terms = value;

    // If we have terms, run a search...
    if ( this.terms.length > 0 ) {
      this.search( this.terms );
    }

    // If search is blank, show all themes
    // Useful for resetting the views when you clean the input
    if ( this.terms === '' ) {
      this.reset( themes.data.themes );
    }

    // Trigger an 'themes:update' event
    this.trigger( 'themes:update' );
  },

  // Controls viewing themes from category on the current theme collection
  // and triggers an update event
  doCategory: function( value ) {  
    //Sets up class for active category button
    $('#theme-search-input').val('');
    $('.theme-categories .theme-category').removeClass('current');
    $('[data-category="'+ value + '"]').addClass('current');

    // Don't do anything if we've already done this search
    // Useful because the Search handler fires multiple times per keystroke
    if ( this.terms === value ) {
      return;
    }

    // Updates terms with the value passed
    this.terms = value;

    // If we have terms, run a search...
    if ( this.terms.length > 0 ) {
      this.category( this.terms );
    }

    // If search is blank, show all themes
    // Useful for resetting the views when you clean the input
    if ( this.terms === '' ) {
      this.reset( themes.data.themes );
    }

    // Trigger an 'themes:update' event
    this.trigger( 'themes:update' );
  },

  // Performs a search within the collection
  // @uses RegExp
  search: function( term ) {
    var match, results, haystack, name, description, author;

    // Start with a full collection
    this.reset( themes.data.themes, { silent: true } );

    // Escape the term string for RegExp meta characters
    term = term.replace( /[-\/\\^$*+?.()|[\]{}]/g, '\\$&' );

    // Consider spaces as word delimiters and match the whole string
    // so matching terms can be combined
    term = term.replace( / /g, ')(?=.*' );
    match = new RegExp( '^(?=.*' + term + ').+', 'i' );

    // Find results
    // _.filter and .test
    results = this.filter( function( data ) {
      name        = data.get( 'name' ).replace( /(<([^>]+)>)/ig, '' );
      description = data.get( 'description' ).replace( /(<([^>]+)>)/ig, '' );
      author      = data.get( 'author' ).replace( /(<([^>]+)>)/ig, '' );

      haystack = _.union( [ name, data.get( 'id' ), description, author, data.get( 'tags' ) ] );

      if ( match.test( data.get( 'author' ) ) && term.length > 2 ) {
        data.set( 'displayAuthor', true );
      }

      return match.test( haystack );
    });

    this.reset( results );
  },

  // Picks categories within the collection
  category: function( term ) {
    var results;

    // Start with a full collection
    this.reset( themes.data.themes, { silent: true } );

    // Find results
    // _.filter and .test
    results = this.filter( function( data ) {
      if(term == 'all' || $.inArray( term, data.attributes.categories_keys ) !== -1)
        return data;
    });

    this.reset( results );
  },

  // Paginates the collection with a helper method
  // that slices the collection
  paginate: function( instance ) {
    var collection = this;
    instance = instance || 0;

    // Themes per instance are set at 15
    collection = _( collection.rest( 10 * instance ) );
    collection = _( collection.first( 10 ) );

    return collection;
  }
});

// This is the view that controls each theme item
// that will be displayed on the screen
themes.view.Theme = wp.Backbone.View.extend({

  // Wrap theme data on a div.theme element
  className: 'theme',

  // Reflects which theme view we have
  // 'grid' (default) or 'detail'
  state: 'grid',

  // The HTML template for each element to be rendered
  html: themes.template( 'theme' ),

  events: {
    'click': 'expand',
    'keydown': 'expand',
    'touchend': 'expand',
    'touchmove': 'preventExpand'
  },

  touchDrag: false,

  render: function() {
    var data = this.model.toJSON();
    // Render themes using the html template
    this.$el.html( this.html( data ) ).attr({
      tabindex: 0,
      'aria-describedby' : data.id + '-action ' + data.id + '-name'
    });

    // Renders active theme styles
    this.activeTheme();
  },

  // Adds a class to the currently active theme
  // and to the overlay in detailed view mode
  activeTheme: function() {
    if ( this.model.get( 'active' ) ) {
      this.$el.addClass( 'active' );
    }
  },
});

// Controls the rendering of div.themes,
// a wrapper that will hold all the theme elements
themes.view.Themes = wp.Backbone.View.extend({

  className: 'themes',
  $overlay: $( 'div.theme-overlay' ),

  // Number to keep track of scroll position
  // while in theme-overlay mode
  index: 0,

  // The theme count element
  count: $( '.theme-count' ),

  initialize: function( options ) {
    var self = this;

    // Set up parent
    this.parent = options.parent;

    // Set current view to [grid]
    this.setView( 'grid' );

    // Move the active theme to the beginning of the collection
    //self.currentTheme();

    // When the collection is updated by user input...
    this.listenTo( self.collection, 'themes:update', function() {
      self.parent.page = 0;
      //self.currentTheme();
      self.render( this );
    });

    this.listenTo( this.parent, 'theme:scroll', function() {
      self.renderThemes( self.parent.page );
    });
  },

  // Manages rendering of theme pages
  // and keeping theme count in sync
  render: function() {
    // Clear the DOM, please
    this.$el.html( '' );

    // Generate the themes
    // Using page instance
    this.renderThemes( this.parent.page );

    // Display a live theme count for the collection
    this.count.text( this.collection.length );
  },

  // Iterates through each instance of the collection
  // and renders each theme module
  renderThemes: function( page ) {
    var self = this;

    self.instance = self.collection.paginate( page );

    // If we have no more themes bail
    if ( self.instance.length === 0 ) {
      return;
    }

    // Loop through the themes and setup each theme view
    self.instance.each( function( theme ) {
      self.theme = new themes.view.Theme({
        model: theme
      });

      // Render the views...
      self.theme.render();
      // and append them to div.themes
      self.$el.append( self.theme.el );

      // Binds to theme:expand to show the modal box
      // with the theme details
      //self.listenTo( self.theme, 'theme:expand', self.expand, self );
    });

    this.parent.page++;
  },

  // Sets current view
  setView: function( view ) {
    return view;
  },
});

// Search input view controller.
themes.view.Search = wp.Backbone.View.extend({

  tagName: 'input',
  className: 'theme-search',
  id: 'theme-search-input',

  attributes: {
    placeholder: l10n.searchFEPlaceholder,
    type: 'search'
  },

  events: {
    'input':  'search',
    'keyup':  'search',
    'change': 'search',
    'search': 'search'
  },

  // Runs a search on the theme collection.
  search: function( event ) {
    // Clear on escape.
    if ( event.type === 'keyup' && event.which === 27 ) {
      event.target.value = '';
    }

    this.collection.doSearch( event.target.value );

    // Update the URL hash
    if ( event.target.value ) {
      themes.router.navigate( themes.router.baseUrl( '?theme-search=' + event.target.value ), { replace: true } );
    } else {
      themes.router.navigate( themes.router.baseUrl( '' ), { replace: true } );
    }
  }
});

// Categories input view controller.
themes.view.Categories = wp.Backbone.View.extend({

  tagName: 'div',
  className: 'theme-categories theme-navigation',

  events: {
    'click a':  'categories'
  },

  render: function() {
    var el = $(this.el);
    el.append( $.parseHTML( '<span class="theme-categories-label">' + l10n.categories + '</span>' ) );
    $.each(categories, function( index, value ) {
      var add_class = '';
      if(index == 'all')
        add_class = ' current';
      el.append( $.parseHTML( '<a data-category="'+ index +'" class="theme-section theme-category'+ add_class +'" href="#">' + value + '</a>' ) );
    });
  },

  // Runs a search on the theme collection.
  categories: function( event ) {
    event.preventDefault();
    
    // Update the URL hash
    if ( event.target.dataset.category ) {
      this.collection.doCategory( event.target.dataset.category );
      themes.router.navigate( themes.router.baseUrl( '?theme-category=' + event.target.dataset.category ), { replace: true } );
    }
  }
});

// Sets up the routes events for relevant url queries
// Listens to [theme] and [search] params
themes.routes = Backbone.Router.extend({

  initialize: function() {
    this.routes = _.object([
    ]);
  },

  baseUrl: function( url ) {
    return themes.data.settings.root + url;
  }
});

// Execute and setup the application
themes.Run = {
  init: function() {
    // Initializes the blog's theme library view
    // Create a new collection with data
    this.themes = new themes.Collection( themes.data.themes );

    // Set up the view
    this.view = new themes.view.Appearance({
      collection: this.themes
    });

    this.render();
  },

  render: function() {
    // Render results
    this.view.render();
    this.routes();

    // Set the initial theme
    if ( 'undefined' !== typeof themes.data.settings.theme && '' !== themes.data.settings.theme ){
      this.view.view.theme.trigger( 'theme:expand', this.view.collection.findWhere( { id: themes.data.settings.theme } ) );
    }

    // Set the initial search
    if ( 'undefined' !== typeof themes.data.settings.search && '' !== themes.data.settings.search ){
      $( '.theme-search' ).val( themes.data.settings.search );
      this.themes.doSearch( themes.data.settings.search );
    }

    // Set the initial category
    if( $('.wmd-themes-showcase').is("[data-category]") ){
      this.themes.doCategory( $('.wmd-themes-showcase').attr('data-category') );
    }
    if ( 'undefined' !== typeof themes.data.settings.category && '' !== themes.data.settings.category ){
      this.themes.doCategory( themes.data.settings.category );
    }

    // Start the router if browser supports History API
    if ( window.history && window.history.pushState ) {
      // Calls the routes functionality
      Backbone.history.start({ pushState: true, silent: true });
    }
  },

  routes: function() {
    // Bind to our global thx object
    // so that the object is available to sub-views
    themes.router = new themes.routes();
  }
};

// Ready...
jQuery( document ).ready(

  // Bring on the themes
  _.bind( themes.Run.init, themes.Run )

);

})( jQuery );