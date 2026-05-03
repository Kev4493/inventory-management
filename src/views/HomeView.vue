<template>
  <div class="card card--offwhite">
    <div class="cardWrapper">
      <div class="cardGreeting">
        <h1>Guten Morgen, Kevin Wagner</h1>
      </div>
      <div class="cardWrapperInner">
        <div class="card card--orange">
          <p class="cardHeading">Mitarbeitende</p>
          <p class="cardCounter">10</p>
        </div>
        <div class="card card--pink">
          <p class="cardHeading">Inventar-Items</p>
          <p class="cardCounter">10</p>
        </div>
        <div class="card card--yellow">
          <p class="cardHeading">zugewiesene Items</p>
          <p class="cardCounter">10</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped lang="scss">
.card {
  padding: 2rem;
  border-radius: 23px;
}

.cardHeading {
  font-weight: 700;
}

.cardWrapperInner {
  display: flex;
  gap: 2rem;
  margin-top: 2rem;

  .card {
    flex: 1;
  }

  .cardCounter {
    font-weight: 700;
    font-size: 3rem;
    margin: 0;
  }
}

// Card colors:
.card--yellow {
  background: linear-gradient(135deg, #f7d96f 0%, #f3c85c 100%);
}

.card--pink {
  background: linear-gradient(135deg, #f58a93 0%, #ef7d89 100%);
}

.card--orange {
  background: linear-gradient(135deg, #f5a16f 0%, #f29a63 100%);
}

.card--offwhite {
  background: linear-gradient(135deg, #f0e5d8 0%, #e8d9c3 100%);
}
</style>
