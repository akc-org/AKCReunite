import { FieldsetGroup } from 'ui/src/components/FieldsetGroup';
import { Fieldset } from 'ui/src/components/Fieldset';
import { Section } from 'ui/src/components/Section';
import { HeaderTitle } from 'ui/src/components/HeaderTitle';
import { Switch } from '@headlessui/react';
import classNames from 'classnames';
import { useEffect, useState } from 'react';
import EventsTableHeader from 'ui/src/components/EventsTableHeader';

declare var wpGoalTrackerGa: any;
declare var wp: any;
declare var lodash: any;

const { apiFetch } = wp;
const { isEqual } = lodash;

type WooCommerceTracking = {
  viewItem: boolean;
  addToCart: boolean;
  viewCart: boolean;
  beginCheckout: boolean;
  addShippingInfo: boolean;
  addPaymentInfo: boolean;
  purchase: boolean;
};

interface EcommerceTrackingSettings {
  wooCommerceSettings?: WooCommerceTracking;
}

const EcommerceTracking = () => {
  const [
    wooCommerceSettings,
    setWooCommerceSettings,
  ] = useState<WooCommerceTracking>({
    viewItem: false,
    addToCart: false,
    viewCart: false,
    beginCheckout: false,
    addShippingInfo: false,
    addPaymentInfo: false,
    purchase: false,
  });

  const [isSaving, setIsSaving] = useState(false),
    [hasNotice, setNotice] = useState(false),
    [hasError, setError] = useState(false),
    [needSave, setNeedSave] = useState(false);

  const actions = [
    {
      id: 'viewItem',
      label: 'View Item',
      description: 'Track when a user views an item.',
    },
    {
      id: 'addToCart',
      label: 'Add to Cart',
      description: 'Track when a user adds an item to their cart.',
    },
    // {
    //   id: 'viewCart',
    //   label: 'View Cart',
    //   description: 'Track when a user views the cart.',
    // },
    {
      id: 'beginCheckout',
      label: 'Begin Checkout',
      description: 'Track when a user starts the checkout process.',
    },
    {
      id: 'addShippingInfo',
      label: 'Add Shipping Info',
      description:
        'Track when the user has entered the shipping info in the checkout process.',
    },
    {
      id: 'addPaymentInfo',
      label: 'Add Payment Info',
      description:
        'Track when the user has entered the payment info in the checkout process.',
    },
    {
      id: 'purchase',
      label: 'Purchase',
      description: 'Track when a user purchases an item.',
    },
  ];

  function isEcommerceSettingsEqual(
    ecommerceTrackingSettings: EcommerceTrackingSettings,
    data: any,
  ) {
    let gsData = { ...data };

    return isEqual(gsData, ecommerceTrackingSettings);
  }

  useEffect(() => {
    const fetchSettings = async () => {
      await getEcommerceTrackingSettings();
    };
    fetchSettings();
  }, []);

  useEffect(() => {
    setNeedSave(true);
  }, [wooCommerceSettings]);

  const updateSettings = (data: EcommerceTrackingSettings) => {
    if (data) {
      if (data.wooCommerceSettings) {
        setWooCommerceSettings(data.wooCommerceSettings);
      }
    }
  };

  async function getEcommerceTrackingSettings() {
    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/get_ecommerce_tracking_settings',
    });
    if (data) {
      updateSettings(data);
      setNeedSave(true);
    } else {
      updateSettings({});
    }
  }

  async function setEcommerceTrackingSettings(event: any) {
    event.preventDefault();
    setIsSaving(true);
    setNeedSave(false);

    const ecommerceTrackingSettings = {
      wooCommerceSettings: wooCommerceSettings,
    };

    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/set_ecommerce_tracking_settings',
      method: 'POST',
      data: { ecommerceTrackingSettings: ecommerceTrackingSettings },
    });

    if (isEcommerceSettingsEqual(ecommerceTrackingSettings, data)) {
      setError(false);
      setIsSaving(false);
      setNeedSave(false);
      updateSettings(data);
    } else {
      setIsSaving(false);
      setError(true);
      setNeedSave(true);
    }
    setNotice(true);
  }

  const handleCheckboxChange = <T,>(
    checked: boolean,
    name: keyof T,
    setState: React.Dispatch<React.SetStateAction<T>>,
  ) => {
    setState(prevSettings => ({
      ...prevSettings,
      [name]: checked,
    }));
  };

  return (
    <div
      data-component="EventsTable"
      className={classNames('pb-6', 'bg-white/50', 'shadow-xl')}
    >
      <EventsTableHeader />
      <div className="bg-white/75">
        <form
          onSubmit={setEcommerceTrackingSettings}
          className="bg-white/50 p-5 rounded shadow-xl"
        >
          <div
            data-component="SectionContainer"
            className="space-y-8 sm:space-y-5"
          >
            <Section>
              <HeaderTitle
                title="WooCommerce Tracking"
                titleHelper={`Track WooCommerce events with Google Analytics`}
                // helpComponent={}
                helpTitle={`WooCommerce`}
                beta={true}
              />
              <FieldsetGroup>
                {actions.map(action => (
                  <Fieldset
                    key={action.id}
                    id={action.id}
                    label={`Track '${action.label}'`}
                    isPrimary={false}
                    description={action.description}
                  >
                    <Switch
                      checked={wooCommerceSettings[action.id]}
                      onChange={checked =>
                        handleCheckboxChange(
                          checked,
                          action.id as keyof WooCommerceTracking,
                          setWooCommerceSettings,
                        )
                      }
                      // Additional Switch props
                      className={classNames(
                        wooCommerceSettings[action.id]
                          ? 'bg-brand-primary'
                          : 'bg-slate-500',
                        'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                      )}
                    >
                      <span className="sr-only">GA4 Debug View</span>
                      <span
                        aria-hidden="true"
                        className={classNames(
                          wooCommerceSettings[action.id]
                            ? 'translate-x-5'
                            : 'translate-x-0',
                          'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                        )}
                      />
                    </Switch>
                  </Fieldset>
                ))}
              </FieldsetGroup>
            </Section>
          </div>
          <footer className="px-5 py-5 bg-gray-100 shadow-2xl -mx-5">
            <div className="flex justify-end space-x-3">
              <button
                type="button"
                className="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                disabled={!needSave}
                className={classNames(
                  'disabled:opacity-30',
                  'inline-flex justify-center',
                  'py-2 px-4',
                  'border border-transparent',
                  'text-sm font-medium rounded-md',
                  'text-white bg-brand-primary hover:bg-indigo-700',
                  'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                  'hover:bg-brand-600 shadow hover:shadow-xl active:shadow-xl',
                  'shadow-sm',
                  'focus:outline-none focus:ring-2 focus:ring-brand-primary-focus focus:ring-offset-2',
                  'transform active:scale-75 hover:scale-105 transition-transform',
                )}
                // className=""
              >
                Save
              </button>
            </div>
          </footer>
        </form>
      </div>
    </div>
  );
};

export default EcommerceTracking;
