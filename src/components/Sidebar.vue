<script setup lang="ts">
import { useRoute } from 'vue-router'
import Logo from '@/assets/Images/anders-und-sehr-logo.svg'

const route = useRoute()

function isActive(path: string) {
  return route.path === path
}
</script>

<template>
  <aside class="sidebar">
    <div class="sidebarHeader">
      <Logo class="logo" />
    </div>
    <nav>
      <ul>
        <li :class="{ active: isActive('/') }">
          <router-link to="/">{{ $t('sidebar.home') }}</router-link>
        </li>
        <li :class="{ active: isActive('/inventory') }">
          <router-link to="/inventory">{{ $t('sidebar.inventory') }}</router-link>
        </li>
        <li :class="{ active: isActive('/addItem') }">
          <router-link to="/addItem">{{ $t('sidebar.addItem') }}</router-link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<style scoped lang="scss">

.sidebarHeader {
  position: absolute;
  top: 0;
  left: 0;
  padding: 2rem;

  .logo {
    object-fit: cover;
    width: 100%;
    height: auto;
    margin-bottom: 2rem;
  }
}

.sidebar {
  width: 200px;
  height: 100vh;
  background-color: $mainBlackBG;
  position: fixed;
  left: 0;
  top: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: end;
}

.sidebar {
  nav {
    color: $mainWhiteText;
  }

  ul {
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    row-gap: 2rem;

    .active {
      background-color: $mainWhiteBG;
    }
  }

  li {
    display: flex;
    align-items: center;
    list-style: none;
    padding-right: 2rem;
    padding-left: 2rem;
    height: 50px;
    border-bottom-left-radius: 12px;
    border-top-left-radius: 12px;

    a {
      text-decoration: none;
      color: $mainWhiteText;

      &:hover {
        color: $brandYellowText;
      }

      &.router-link-active {
        color: $mainBlackText;
      }
    }
  }
}
</style>
