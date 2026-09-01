<template>
  <div class="grape-wrapper">
    <!-- Screen-wide Full-Bleed Hero Section -->
    <header class="hero-section">
      <div class="hero-bg"></div>
      <div class="hero-container">
        <div class="hero-meta">
          <span>potagerphp/grape</span>
          <span class="sep">/</span>
          <span>PHP 8.2+</span>
          <span class="sep">/</span>
          <span>Zero dependencies</span>
        </div>

        <h1 class="hero-title">
          Type-safe schema validation<br />
          and data sanitization for PHP.
        </h1>

        <p class="hero-desc">
          Define declarative contracts, parse untrusted inputs, sanitize values on-the-fly, and receive strongly typed output or precise error maps.
        </p>

        <div class="hero-actions">
          <a href="/guide/getting-started" class="btn-solid">
            Documentation &rarr;
          </a>
          <a href="/guide/introduction" class="btn-outline">
            Introduction
          </a>
          <div class="terminal-pill" @click="copyCommand">
            <span class="term-prompt">$</span>
            <span class="term-cmd">composer require potagerphp/grape</span>
            <span class="copy-badge" :class="{ copied }">
              <svg v-if="!copied" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
              </svg>
              <svg v-else viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </span>
          </div>
        </div>
      </div>

      <!-- Elegant Arc of Circle Bottom Transition -->
      <div class="hero-arc-separator">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path d="M0,0 C360,75 1080,75 1440,0 L1440,80 L0,80 Z" fill="var(--vp-c-bg)" />
          <path d="M0,0 C360,75 1080,75 1440,0" stroke="var(--vp-c-divider)" stroke-width="1" fill="none" />
        </svg>
      </div>
    </header>

    <!-- Centered Content Wrapper -->
    <main class="grape-content">
      <!-- Concise Interactive Example -->
      <section class="content-section">
        <div class="section-header">
          <span class="section-tag">Quickstart</span>
          <h2 class="section-title">How it works</h2>
        </div>

        <div class="code-editor">
          <nav class="code-tabs">
            <button
              v-for="(tab, index) in tabs"
              :key="index"
              :class="['code-tab', { active: activeTab === index }]"
              @click="activeTab = index"
            >
              {{ tab.title }}
            </button>
          </nav>

          <div class="code-content">
            <pre class="syntax-code" v-html="tabs[activeTab].html"></pre>
          </div>
        </div>
      </section>

      <!-- Core Philosophy: Clean Editorial Pairs -->
      <section class="content-section">
        <div class="section-header">
          <span class="section-tag">Design Principles</span>
          <h2 class="section-title">Core capabilities</h2>
        </div>

        <div class="feature-list">
          <!-- Item 1: Validation -->
          <div class="feature-row">
            <div class="feature-info">
              <h3 class="feature-title">Declarative Validation</h3>
              <p class="feature-desc">
                Declare strict constraints with IDE-autocompleted chainable methods. Enforce length boundaries, regex patterns, email formats, and presence requirements (<code>required()</code> / <code>nullable()</code>).
              </p>
            </div>
            <div class="feature-code">
              <pre class="syntax-code"><span class="c-var">$validator</span> = <span class="c-type">Grape</span>::<span class="c-fn">schema</span>([
    <span class="c-str">'username'</span> =&gt; <span class="c-type">Grape</span>::<span class="c-fn">string</span>()-&gt;<span class="c-fn">minLength</span>(<span class="c-num">3</span>)-&gt;<span class="c-fn">maxLength</span>(<span class="c-num">20</span>)-&gt;<span class="c-fn">required</span>(),
    <span class="c-str">'email'</span>    =&gt; <span class="c-type">Grape</span>::<span class="c-fn">string</span>()-&gt;<span class="c-fn">email</span>()-&gt;<span class="c-fn">required</span>(),
    <span class="c-str">'age'</span>      =&gt; <span class="c-type">Grape</span>::<span class="c-fn">integer</span>()-&gt;<span class="c-fn">min</span>(<span class="c-num">18</span>)-&gt;<span class="c-fn">optional</span>(),
]);</pre>
            </div>
          </div>

          <!-- Item 2: Sanitization -->
          <div class="feature-row">
            <div class="feature-info">
              <h3 class="feature-title">In-Place Data Sanitization</h3>
              <p class="feature-desc">
                Clean and normalize values directly during validation. Methods like <code>trim()</code>, <code>lowercase()</code>, <code>clamp()</code>, and <code>compact()</code> transform the data so your domain receives sanitized output.
              </p>
            </div>
            <div class="feature-code">
              <pre class="syntax-code"><span class="c-var">$clean</span> = <span class="c-type">Grape</span>::<span class="c-fn">schema</span>([
    <span class="c-str">'search'</span> =&gt; <span class="c-type">Grape</span>::<span class="c-fn">string</span>()-&gt;<span class="c-fn">trim</span>()-&gt;<span class="c-fn">lowercase</span>(),
    <span class="c-str">'limit'</span>  =&gt; <span class="c-type">Grape</span>::<span class="c-fn">integer</span>()-&gt;<span class="c-fn">clamp</span>(<span class="c-num">1</span>, <span class="c-num">100</span>),
])-&gt;<span class="c-fn">validate</span>(<span class="c-var">$_GET</span>);</pre>
            </div>
          </div>

          <!-- Item 3: Type Coercion -->
          <div class="feature-row">
            <div class="feature-info">
              <h3 class="feature-title">Strict &amp; Loose Type Coercion</h3>
              <p class="feature-desc">
                Loose mode (default) seamlessly coerces HTTP form strings like <code>"28" &rarr; 28</code> and <code>"true" &rarr; true</code>. For strict JSON APIs, pass <code>strict: true</code> to enforce exact native types without casting.
              </p>
            </div>
            <div class="feature-code">
              <pre class="syntax-code"><span class="c-type">Grape</span>::<span class="c-fn">number</span>();              <span class="c-cm">// Loose (casts numeric strings)</span>
<span class="c-type">Grape</span>::<span class="c-fn">number</span>(strict: <span class="c-kw">true</span>);  <span class="c-cm">// Strict (int &amp; float only)</span>
<span class="c-type">Grape</span>::<span class="c-fn">boolean</span>(strict: <span class="c-kw">true</span>); <span class="c-cm">// Strict (bool only)</span></pre>
            </div>
          </div>

          <!-- Item 4: Composition -->
          <div class="feature-row">
            <div class="feature-info">
              <h3 class="feature-title">First-Class Composition</h3>
              <p class="feature-desc">
                Nest associative schemas, homogeneous collections, and fixed-size tuples. Collections support deduplication (<code>distinct()</code>), invalid item skipping (<code>skipInvalids()</code>), and index normalization.
              </p>
            </div>
            <div class="feature-code">
              <pre class="syntax-code"><span class="c-var">$order</span> = <span class="c-type">Grape</span>::<span class="c-fn">schema</span>([
    <span class="c-str">'tags'</span>        =&gt; <span class="c-type">Grape</span>::<span class="c-fn">collection</span>(<span class="c-type">Grape</span>::<span class="c-fn">string</span>())-&gt;<span class="c-fn">distinct</span>(),
    <span class="c-str">'coordinates'</span> =&gt; <span class="c-type">Grape</span>::<span class="c-fn">tuple</span>([<span class="c-type">Grape</span>::<span class="c-fn">float</span>(), <span class="c-type">Grape</span>::<span class="c-fn">float</span>()]),
]);</pre>
            </div>
          </div>

          <!-- Item 5: Non-Throwing Flow -->
          <div class="feature-row">
            <div class="feature-info">
              <h3 class="feature-title">Non-Throwing Result Option</h3>
              <p class="feature-desc">
                Choose your error handling style. Throw structured <code>ValidationException</code> or use the functional <code>check()</code> method returning <code>[$error, $data]</code> to handle errors without try/catch blocks.
              </p>
            </div>
            <div class="feature-code">
              <pre class="syntax-code">[<span class="c-var">$error</span>, <span class="c-var">$data</span>] = <span class="c-var">$schema</span>-&gt;<span class="c-fn">check</span>(<span class="c-var">$payload</span>);

<span class="c-kw">if</span> (<span class="c-var">$error</span> !== <span class="c-kw">null</span>) {
    <span class="c-kw">return</span> <span class="c-fn">response</span>()-&gt;<span class="c-fn">json</span>(<span class="c-var">$error</span>-&gt;<span class="c-fn">getMessages</span>(), <span class="c-num">422</span>);
}</pre>
            </div>
          </div>
        </div>
      </section>

      <!-- Reference Directory -->
      <section class="content-section">
        <div class="section-header">
          <span class="section-tag">Supported Types</span>
          <h2 class="section-title">Built-in types and structures</h2>
        </div>

        <div class="type-grid">
          <a href="/validator/types/string" class="type-link">
            <span>string()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/number" class="type-link">
            <span>number()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/integer" class="type-link">
            <span>integer()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/float" class="type-link">
            <span>float()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/boolean" class="type-link">
            <span>boolean()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/accepted" class="type-link">
            <span>accepted()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/schema" class="type-link">
            <span>schema()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/collection" class="type-link">
            <span>collection()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/tuple" class="type-link">
            <span>tuple()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/literal" class="type-link">
            <span>literal()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/null" class="type-link">
            <span>null()</span>
            <span class="arrow">&rarr;</span>
          </a>
          <a href="/validator/types/mixed" class="type-link">
            <span>mixed()</span>
            <span class="arrow">&rarr;</span>
          </a>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const copied = ref(false);
const activeTab = ref(0);

const copyCommand = async () => {
  try {
    await navigator.clipboard.writeText('composer require potagerphp/grape');
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 1800);
  } catch (err) {
    // Fallback
  }
};

const tabs = [
  {
    title: 'Validate & Sanitize',
    html: `<span class="c-kw">use</span> <span class="c-type">Potager\\Grape\\Grape</span>;

<span class="c-var">$schema</span> = <span class="c-type">Grape</span>::<span class="c-fn">schema</span>([
    <span class="c-str">'name'</span>  =&gt; <span class="c-type">Grape</span>::<span class="c-fn">string</span>()-&gt;<span class="c-fn">trim</span>()-&gt;<span class="c-fn">minLength</span>(<span class="c-num">2</span>)-&gt;<span class="c-fn">required</span>(),
    <span class="c-str">'email'</span> =&gt; <span class="c-type">Grape</span>::<span class="c-fn">string</span>()-&gt;<span class="c-fn">trim</span>()-&gt;<span class="c-fn">lowercase</span>()-&gt;<span class="c-fn">email</span>()-&gt;<span class="c-fn">required</span>(),
    <span class="c-str">'age'</span>   =&gt; <span class="c-type">Grape</span>::<span class="c-fn">integer</span>()-&gt;<span class="c-fn">min</span>(<span class="c-num">18</span>)-&gt;<span class="c-fn">optional</span>(),
]);

<span class="c-cm">// Validates and returns clean, typed data:</span>
<span class="c-var">$data</span> = <span class="c-var">$schema</span>-&gt;<span class="c-fn">validate</span>(<span class="c-var">$_POST</span>);`
  },
  {
    title: 'Non-throwing check()',
    html: `<span class="c-kw">use</span> <span class="c-type">Potager\\Grape\\Grape</span>;

<span class="c-cm">// Returns [$error, $data] tuple without try/catch:</span>
[<span class="c-var">$error</span>, <span class="c-var">$data</span>] = <span class="c-var">$schema</span>-&gt;<span class="c-fn">check</span>(<span class="c-var">$payload</span>);

<span class="c-kw">if</span> (<span class="c-var">$error</span> !== <span class="c-kw">null</span>) {
    <span class="c-kw">return</span> <span class="c-fn">response</span>()-&gt;<span class="c-fn">json</span>([<span class="c-str">'errors'</span> =&gt; <span class="c-var">$error</span>-&gt;<span class="c-fn">getMessages</span>()], <span class="c-num">422</span>);
}

<span class="c-cm">// $data is guaranteed clean and typed</span>
<span class="c-fn">handleSuccess</span>(<span class="c-var">$data</span>);`
  },
  {
    title: 'Error structure',
    html: `<span class="c-cm">// Structured error tree returned by $error->getMessages():</span>
[
    <span class="c-str">'email'</span> =&gt; [
        [
            <span class="c-str">'message'</span> =&gt; <span class="c-str">'The email field must be a valid email address.'</span>,
            <span class="c-str">'rule'</span>    =&gt; <span class="c-str">'email'</span>,
            <span class="c-str">'path'</span>    =&gt; <span class="c-str">'email'</span>,
        ]
    ]
]`
  }
];
</script>
