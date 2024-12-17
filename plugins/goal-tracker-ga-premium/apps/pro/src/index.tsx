declare var wp: any;
declare var wpGoalTrackerGa: any;

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import { createRoot } from 'react-dom/client';
import {
  AdjustmentsIcon,
  ArrowRightIcon,
  QuestionMarkCircleIcon,
} from '@heroicons/react/solid';
import { TabContextProvider } from 'ui/src/context/TabContext';
import './main.css';

/*Code goes here
 * Output : build/index.js
 * */

const { __ } = wp.i18n;

const { render, useState } = wp.element;

const { apiFetch } = wp;

const {
  TabPanel,
  Notice,
  Button,
  Card,
  CardHeader,
  CardBody,
  CardDivider,
  CardFooter,
  Spinner,
} = wp.components;

import { HashRouter, Route, Routes } from 'react-router-dom';

import { PluginFooter } from 'ui';
import PluginNav from 'ui/src/components/nav';

import classNames from 'classnames';
import { GettingStartedGuide } from 'ui';
import HelpSliderProvider from 'ui/src/context/HelpSliderProvider';
import { NavigationProvider } from 'ui/src/context/NavigationContext';
import { PromoContextProvider } from 'ui/src/context/PromoContext';
import CustomEvents from './components/CustomEvents';
import Settings from './components/Settings';
import { GeneralSettingsProvider } from './context/GeneralSettingsContext';
import { MigrateToGoalTracker } from 'ui/src/components/MigrateToGoalTracker';
import React from 'react';

const initialTabs = [
  {
    name: 'Tracker',
    href: '/tracker',
    current: true,
    primary: true,
    hasIssue: false,
    rootTab: false,
  },
  {
    name: 'Migrate',
    href: '/migrate',
    hasIssue: false,
    rootTab: false,
  },
  {
    name: 'Settings',
    href: '/settings',
    // rootTab: true,
    isFirstTime: true,
    rootTab: false,
    hasIssue: false,
    icon: <AdjustmentsIcon className="h-8 w-8" aria-hidden="true" />,
  },
  {
    name: 'Help',
    showTitle: false,
    href: '/help',
    firstTime: true,
    rootTab: false,
    hasIssue: false,
    icon: <QuestionMarkCircleIcon className="h-8 w-8" aria-hidden="true" />,
  },
];

const navLinks = [
  { label: 'Click', path: '/tracker/click-tracking', default: true },
  { label: 'Visibility', path: '/tracker/visibility-tracking', default: false },
  {
    label: 'WooCommerce',
    path: '/tracker/ecommerce-tracking',
    default: false,
  },
  { label: 'Video', path: '/tracker/video-tracking', default: false },
  { label: 'Forms', path: '/tracker/form-tracking', default: false },
];

const getComponentForRootPath = (tabName: string) => {
  switch (tabName) {
    case 'Tracker':
      return <CustomEvents />;
    case 'Settings':
      return <Settings />;
    default:
      return <Settings />;
  }
};

const AddSettings = () => {
  const rootElement = document.getElementById(wpGoalTrackerGa.root_id);
  const initialPrimaryTab = rootElement?.getAttribute('data-primary-tab');

  const componentForRootPath = getComponentForRootPath(initialPrimaryTab ?? '');

  const modifiedInitialTabs = initialTabs.map(tab => ({
    ...tab,
    rootTab: tab.name === initialPrimaryTab ? true : tab.rootTab,
  }));

  return (
    <TabContextProvider initialTabs={modifiedInitialTabs}>
      <GeneralSettingsProvider>
        <NavigationProvider navLinks={navLinks}>
          <PromoContextProvider>
            <HelpSliderProvider>
              <section data-component="PluginMain" className="relative">
                <HashRouter>
                  <PluginNav></PluginNav>
                  <Routes>
                    <Route path="/tracker/*" element={<CustomEvents />} />
                    <Route
                      path={'/migrate'}
                      element={<MigrateToGoalTracker />}
                    ></Route>
                    <Route
                      path={'/help'}
                      element={<GettingStartedGuide />}
                    ></Route>
                    <Route path={'/settings'} element={<Settings />}></Route>
                    <Route path="/" element={componentForRootPath}></Route>
                  </Routes>
                </HashRouter>
                <PluginFooter />
              </section>
            </HelpSliderProvider>
          </PromoContextProvider>
        </NavigationProvider>
      </GeneralSettingsProvider>
    </TabContextProvider>
  );
};
document.addEventListener('DOMContentLoaded', () => {
  const reactVersion = React.version.split('.').map(Number);
  if (reactVersion[0] >= 18) {
    const rootElement = document.getElementById(wpGoalTrackerGa.root_id);
    if (rootElement) {
      const root = createRoot(rootElement);
      root.render(<AddSettings />);
    }
  } else {
    if (
      'undefined' !== typeof document.getElementById(wpGoalTrackerGa.root_id) &&
      null !== document.getElementById(wpGoalTrackerGa.root_id)
    ) {
      render(<AddSettings />, document.getElementById(wpGoalTrackerGa.root_id));
    }
  }
});
