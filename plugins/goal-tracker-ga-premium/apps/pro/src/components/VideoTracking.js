import { Switch } from '@headlessui/react';
import { useEffect, useState } from 'react';
import EventsTableHeader from 'ui/src/components/EventsTableHeader';

const { apiFetch } = wp;

const { isEqual } = lodash;

function classNames(...classes) {
  return classes.filter(Boolean).join(' ');
}

import {
  useComponentDidUpdate,
  useComponentWillUnmount,
} from '../utils/components';

const VideoTracking = () => {
  const [gaYoutubeVideoTracking, setGaYoutubeVideoTracking] = useState(false);
  const [gaVimeoVideoTracking, setGaVimeoVideoTracking] = useState(false);
  const [gaMediaVideoTracking, setGaMediaVideoTracking] = useState(false);
  const [gaMediaAudioTracking, setGaMediaAudioTracking] = useState(false);

  const [disablePageView, setDisablePageView] = useState(false);

  const [isSaving, setIsSaving] = useState(false),
    [hasNotice, setNotice] = useState(false),
    [hasError, setError] = useState(false),
    [needSave, setNeedSave] = useState(false);

  const SettingNotice = () => (
    <Notice
      onRemove={() => setNotice(false)}
      status={hasError ? 'error' : 'success'}
    >
      <p>
        {hasError && __('An error occurred.', 'wp-goal-tracker-ga')}
        {!hasError && __('Saved Successfully.', 'wp-goal-tracker-ga')}
      </p>
    </Notice>
  );

  useEffect(() => {
    const fetchSettings = async () => {
      await getVideoSettings();
    };
    fetchSettings();
  }, []);

  useEffect(() => {
    setNeedSave(true);
  }, [
    gaYoutubeVideoTracking,
    gaVimeoVideoTracking,
    gaMediaVideoTracking,
    gaMediaAudioTracking,
  ]);

  const updateSettings = data => {
    if (data) {
      if (data.gaYoutubeVideoTracking) {
        setGaYoutubeVideoTracking(data.gaYoutubeVideoTracking);
      }

      if (data.gaVimeoVideoTracking) {
        setGaVimeoVideoTracking(data.gaVimeoVideoTracking);
      }

      if (data.gaMediaVideoTracking) {
        setGaMediaVideoTracking(data.gaMediaVideoTracking);
      }

      if (data.gaMediaAudioTracking) {
        setGaMediaAudioTracking(data.gaMediaAudioTracking);
      }
    }
  };

  function isVideoSettingsEqual(videoSettings, data) {
    let gsData = { ...data };

    return isEqual(gsData, videoSettings);
  }

  async function getVideoSettings() {
    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/get_video_settings',
    });
    if (data) {
      updateSettings(data);
      setNeedSave(true);
    } else {
      updateSettings({});
    }
  }

  async function setVideoSettings(event) {
    event.preventDefault();
    setIsSaving(true);
    setNeedSave(false);
    const videoSettings = {
      gaYoutubeVideoTracking: gaYoutubeVideoTracking,
      gaVimeoVideoTracking: gaVimeoVideoTracking,
      gaMediaVideoTracking: gaMediaVideoTracking,
      gaMediaAudioTracking: gaMediaAudioTracking,
    };

    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/set_video_settings',
      method: 'POST',
      data: { videoSettings: videoSettings },
    });

    if (isVideoSettingsEqual(videoSettings, data)) {
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

  useComponentDidUpdate(() => {
    /*Nothing for now*/
  });

  useComponentWillUnmount(() => {
    /*Nothing for now*/
  });

  return (
    <div
      data-component="EventsTable"
      className={classNames('pb-6', 'bg-white/50', 'shadow-xl')}
    >
      <EventsTableHeader
        setAddCustomEventForm={{}}
        setAddRecommendedEventForm={{}}
      />
      <div className="mt-8 flex flex-col px-4">
        <form
          onSubmit={setVideoSettings}
          className={classNames(
            // 'max-w-5xl',
            'w-full',
            'pt-10 px-6',
            'space-y-8 divide-y divide-gray-200 rounded-md',
            'border border-gray-200',
            'bg-white shadow',
          )}
        >
          <div className="space-y-8 divide-y divide-gray-200 sm:space-y-5">
            <div>
              <div>
                <h3 className="text-2xl leading-6 font-medium text-gray-900">
                  Video Tracking
                </h3>
              </div>
            </div>

            <div className="space-y-6 sm:space-y-5 divide-y divide-gray-200">
              <div className="pt-6 sm:pt-5">
                <div role="group" aria-labelledby="label-track-Links">
                  <div className="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                    <div>
                      <div
                        className="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700"
                        id="label-youtube-video-tracking"
                      >
                        Track YouTube Videos
                      </div>
                    </div>
                    <div className="mt-4 sm:mt-0 sm:col-span-2">
                      <Switch
                        id="gaYoutubeVideoTracking"
                        name="gaYoutubeVideoTracking"
                        checked={gaYoutubeVideoTracking}
                        onChange={setGaYoutubeVideoTracking}
                        className={classNames(
                          gaYoutubeVideoTracking
                            ? 'bg-brand-primary'
                            : 'bg-gray-200',
                          'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                        )}
                      >
                        <span className="sr-only">Use setting</span>
                        <span
                          aria-hidden="true"
                          className={classNames(
                            gaYoutubeVideoTracking
                              ? 'translate-x-5'
                              : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                          )}
                        />
                      </Switch>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div className="space-y-6 sm:space-y-5 divide-y divide-gray-200">
              <div className="pt-6 sm:pt-5">
                <div role="group" aria-labelledby="label-track-Links">
                  <div className="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                    <div>
                      <div
                        className="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700"
                        id="label-vimeo-video-tracking"
                      >
                        Track Vimeo Videos
                      </div>
                    </div>
                    <div className="mt-4 sm:mt-0 sm:col-span-2">
                      <Switch
                        id="gaVimeoTracking"
                        name="gaVimeoTracking"
                        checked={gaVimeoVideoTracking}
                        onChange={setGaVimeoVideoTracking}
                        className={classNames(
                          gaVimeoVideoTracking
                            ? 'bg-brand-primary'
                            : 'bg-gray-200',
                          'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                        )}
                      >
                        <span className="sr-only">Use setting</span>
                        <span
                          aria-hidden="true"
                          className={classNames(
                            gaVimeoVideoTracking
                              ? 'translate-x-5'
                              : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                          )}
                        />
                      </Switch>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div className="space-y-6 sm:space-y-5 divide-y divide-gray-200">
              <div className="pt-6 sm:pt-5">
                <div role="group" aria-labelledby="label-track-Links">
                  <div className="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                    <div>
                      <div
                        className="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700"
                        id="label-media-video-tracking"
                      >
                        Track Self-Hosted Media Videos
                      </div>
                    </div>
                    <div className="mt-4 sm:mt-0 sm:col-span-2">
                      <Switch
                        id="gaMediaVideoTracking"
                        name="gaMediaVideoTracking"
                        checked={gaMediaVideoTracking}
                        onChange={setGaMediaVideoTracking}
                        className={classNames(
                          gaMediaVideoTracking
                            ? 'bg-brand-primary'
                            : 'bg-gray-200',
                          'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                        )}
                      >
                        <span className="sr-only">Use setting</span>
                        <span
                          aria-hidden="true"
                          className={classNames(
                            gaMediaVideoTracking
                              ? 'translate-x-5'
                              : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                          )}
                        />
                      </Switch>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="space-y-8 divide-y divide-gray-200 sm:space-y-5">
            <div>
              <div>
                <h3 className="text-2xl leading-6 font-medium text-gray-900 mt-5">
                  Audio Tracking
                </h3>
              </div>
            </div>

            <div className="space-y-6 sm:space-y-5 divide-y divide-gray-200">
              <div className="pt-6 sm:pt-5">
                <div role="group" aria-labelledby="label-track-Links">
                  <div className="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                    <div>
                      <div
                        className="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700"
                        id="label-media-audio-tracking"
                      >
                        Track Self Hosted Audio
                      </div>
                    </div>
                    <div className="mt-4 sm:mt-0 sm:col-span-2">
                      <Switch
                        id="gaMediaAudioTracking"
                        name="gaMediaAudioTracking"
                        checked={gaMediaAudioTracking}
                        onChange={setGaMediaAudioTracking}
                        className={classNames(
                          gaMediaAudioTracking
                            ? 'bg-brand-primary'
                            : 'bg-gray-200',
                          'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                        )}
                      >
                        <span className="sr-only">Use setting</span>
                        <span
                          aria-hidden="true"
                          className={classNames(
                            gaMediaAudioTracking
                              ? 'translate-x-5'
                              : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                          )}
                        />
                      </Switch>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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

export default VideoTracking;
