import React, { Fragment } from "react";
import { Menu, Transition } from '@headlessui/react'
import { MenuIcon, PlusIcon } from '@heroicons/react/solid'

function classNames(...classes) {
  return classes.filter(Boolean).join(' ')
}

const items = [
  { name: '$$PAGENAME$$', description: 'Current page name' },
  { name: '$$POST_ID$$', description: 'Id of the current post or page' },
  { name: '$$CATEGORY$$', description: 'Post or page category' },
  { name: '$$ELEMENT_TEXT$$', description: 'Text of tracked element' },
  { name: '$$AUTHOR$$', description: 'Name of author' },
  { name: '$$REFERRER$$', description: 'Referrer if exists' },
  { name: '$$USER$$', description: 'User name' },
  { name: '$$PAGE_URL$$', description: 'Url of current page' },
  { name: '$$IS_LOGGED_IN$$', description: 'Is this a logged in user' },
  { name: '$$USER_ID$$', description: 'Logged in user id' },
  { name: '$$ATTR_$$', description: 'HTML attribute. Append attribute name after ATTR_ for example, $$ATTR_href$$' },
]

const PlaceholderButton = ({ setPropValue }) => {
  return (
    <Menu as="div" className="relative -ml-px block">
      <Menu.Button className="relative inline-flex items-center rounded-r-md border border-brand-input bg-brand-primary px-2 py-1 text-sm font-medium text-white hover:bg-brand-primary-hover focus:z-10 focus:border-brand-primary-focus focus:outline-none focus:ring-1 focus:ring-indigo-500">
        <span className="sr-only">Open options</span>
        <MenuIcon className="h-5 w-5" aria-hidden="true" />
        <PlusIcon className="h-3 w-3" aria-hidden="true" />
      </Menu.Button>
      <Transition
        as={Fragment}
        enter="transition ease-out duration-100"
        enterFrom="transform opacity-0 scale-95"
        enterTo="transform opacity-100 scale-100"
        leave="transition ease-in duration-75"
        leaveFrom="transform opacity-100 scale-100"
        leaveTo="transform opacity-0 scale-95"
      >
        <Menu.Items className="absolute right-0 z-10 mt-2 -mr-1 w-[30rem] origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none ">
          <div className="py-1  overflow-scroll h-64">
            <label htmlFor="selectorType" className="block ml-4 pb-1 text-sm font-medium border-b sm:border-gray-200 text-gray-700 sm:mt-px sm:pt-2">
              Dynanic Properties (Placeholders)
            </label>
            {items.map((item) => (
              <Menu.Item key={item.name}>
                {({ active }) => (
                  <div className="flex flex-1 my-2">
                    <div className="flex-1">
                      <span
                        onClick={() => setPropValue(item.name)}
                        className={classNames(
                          active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                          'ml-4 rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800 flex-none cursor-pointer'
                        )}
                      >
                        {item.name}
                      </span>
                    </div>
                    <span className="text-gray-700 block px-2.5 py-0.5 text-xs flex-1">{item.description}</span>
                  </div>
                )}
              </Menu.Item>
            ))}
          </div>
        </Menu.Items>
      </Transition>
    </Menu>
  )
}

export default PlaceholderButton;