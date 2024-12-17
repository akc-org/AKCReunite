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

type ContactForm7 = {
  trackFormSubmit: boolean;
  trackInvalids: boolean;
  trackMailSent: boolean;
  trackMailFailed: boolean;
  trackSpam: boolean;
};

interface FormTrackingSettings {
  contactForm7Settings?: ContactForm7;
}

const FormTracking = () => {
  const [
    contactForm7Settings,
    setContactForm7Settings,
  ] = useState<ContactForm7>({
    trackFormSubmit: false,
    trackInvalids: false,
    trackMailSent: false,
    trackMailFailed: false,
    trackSpam: false,
  });

  const [isSaving, setIsSaving] = useState(false),
    [hasNotice, setNotice] = useState(false),
    [hasError, setError] = useState(false),
    [needSave, setNeedSave] = useState(false);

  function isFormTrackingSettingsEqual(
    formTrackingSettings: FormTrackingSettings,
    data: any,
  ) {
    let gsData = { ...data };

    return isEqual(gsData, formTrackingSettings);
  }

  useEffect(() => {
    const fetchSettings = async () => {
      await getFormTrackingSettings();
    };
    fetchSettings();
  }, []);

  useEffect(() => {
    setNeedSave(true);
  }, [contactForm7Settings]);

  const updateSettings = (data: FormTrackingSettings) => {
    if (data) {
      if (data.contactForm7Settings) {
        setContactForm7Settings(data.contactForm7Settings);
      }
    }
  };

  async function getFormTrackingSettings() {
    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/get_form_tracking_settings',
    });
    if (data) {
      updateSettings(data);
      setNeedSave(true);
    } else {
      updateSettings({});
    }
  }

  async function setFormTrackingSettings(event: any) {
    event.preventDefault();
    setIsSaving(true);
    setNeedSave(false);

    const formTrackingSettings = {
      contactForm7Settings: contactForm7Settings,
    };

    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/set_form_tracking_settings',
      method: 'POST',
      data: { formTrackingSettings: formTrackingSettings },
    });

    if (isFormTrackingSettingsEqual(formTrackingSettings, data)) {
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
          onSubmit={setFormTrackingSettings}
          className="bg-white/50 p-5 rounded shadow-xl"
        >
          <div
            data-component="SectionContainer"
            className="space-y-8 sm:space-y-5"
          >
            <Section>
              <HeaderTitle
                title={`Contact Form 7`}
                titleHelper={`Enable tracking for Contact Form 7 forms on your website.`}
                // helpComponent={}
                helpTitle={`Connecting with Google Analytics`}
              />
              <FieldsetGroup>
                <Fieldset
                  label={'Track Successful Form Submissions'}
                  id=""
                  description="The plugin will track an event only when a form submission is successful, and an email is sent."
                  isPrimary={false}
                >
                  <Switch
                    id="trackMailSent"
                    name="trackMailSent"
                    checked={contactForm7Settings.trackMailSent}
                    onChange={checked =>
                      handleCheckboxChange(
                        checked,
                        'trackMailSent',
                        setContactForm7Settings,
                      )
                    }
                    className={classNames(
                      contactForm7Settings.trackMailSent
                        ? 'bg-brand-primary'
                        : 'bg-slate-500',
                      'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                    )}
                  >
                    <span className="sr-only">GA4 Debug View</span>
                    <span
                      aria-hidden="true"
                      className={classNames(
                        contactForm7Settings.trackMailSent
                          ? 'translate-x-5'
                          : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      )}
                    />
                  </Switch>
                </Fieldset>

                <Fieldset
                  label={'Track Failed Form Validations'}
                  id=""
                  description="Track and event when someone submits an invalid form (mostly missing required fields)."
                  isPrimary={false}
                >
                  <Switch
                    id="trackInvalids"
                    name="trackInvalids"
                    checked={contactForm7Settings.trackInvalids}
                    onChange={checked =>
                      handleCheckboxChange(
                        checked,
                        'trackInvalids',
                        setContactForm7Settings,
                      )
                    }
                    className={classNames(
                      contactForm7Settings.trackInvalids
                        ? 'bg-brand-primary'
                        : 'bg-slate-500',
                      'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                    )}
                  >
                    <span className="sr-only">GA4 Debug View</span>
                    <span
                      aria-hidden="true"
                      className={classNames(
                        contactForm7Settings.trackInvalids
                          ? 'translate-x-5'
                          : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      )}
                    />
                  </Switch>
                </Fieldset>

                <Fieldset
                  label={'Track "Failed To Send Email" Events'}
                  id=""
                  description="When the form submits successfully but Contact Form 7 failed to send out the email."
                  isPrimary={false}
                >
                  <Switch
                    id="trackMailFailed"
                    name="trackMailFailed"
                    checked={contactForm7Settings.trackMailFailed}
                    onChange={checked =>
                      handleCheckboxChange(
                        checked,
                        'trackMailFailed',
                        setContactForm7Settings,
                      )
                    }
                    className={classNames(
                      contactForm7Settings.trackMailFailed
                        ? 'bg-brand-primary'
                        : 'bg-slate-500',
                      'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                    )}
                  >
                    <span className="sr-only">GA4 Debug View</span>
                    <span
                      aria-hidden="true"
                      className={classNames(
                        contactForm7Settings.trackMailFailed
                          ? 'translate-x-5'
                          : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      )}
                    />
                  </Switch>
                </Fieldset>

                <Fieldset
                  label={'Track SPAM'}
                  id=""
                  description="When Contact Form 7 detects possible spam activity."
                  isPrimary={false}
                >
                  <Switch
                    id="trackSpam"
                    name="trackSpam"
                    checked={contactForm7Settings.trackSpam}
                    onChange={checked =>
                      handleCheckboxChange(
                        checked,
                        'trackSpam',
                        setContactForm7Settings,
                      )
                    }
                    className={classNames(
                      contactForm7Settings.trackSpam
                        ? 'bg-brand-primary'
                        : 'bg-slate-500',
                      'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                    )}
                  >
                    <span className="sr-only">GA4 Debug View</span>
                    <span
                      aria-hidden="true"
                      className={classNames(
                        contactForm7Settings.trackSpam
                          ? 'translate-x-5'
                          : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      )}
                    />
                  </Switch>
                </Fieldset>

                <Fieldset
                  label={'Track Form Submit Click (click ≠ success)'}
                  id=""
                  description="Toggling this option will track submit button clicks regardless of the submission status (click ≠ success)."
                  isPrimary={false}
                >
                  <Switch
                    id="trackSpam"
                    name="trackSpam"
                    checked={contactForm7Settings.trackFormSubmit}
                    onChange={checked =>
                      handleCheckboxChange(
                        checked,
                        'trackFormSubmit',
                        setContactForm7Settings,
                      )
                    }
                    className={classNames(
                      contactForm7Settings.trackFormSubmit
                        ? 'bg-brand-primary'
                        : 'bg-slate-500',
                      'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                    )}
                  >
                    <span className="sr-only">GA4 Debug View</span>
                    <span
                      aria-hidden="true"
                      className={classNames(
                        contactForm7Settings.trackFormSubmit
                          ? 'translate-x-5'
                          : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      )}
                    />
                  </Switch>
                </Fieldset>
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
export default FormTracking;
