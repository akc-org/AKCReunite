import ProLabel from '../buttons/ProLabel';

const PageSpeedTrackingHelp = () => {
  return (
    <div className="mt-5">
      <div className="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
        Page Speed Tracking (Load Time) <ProLabel />
      </div>
      <span className="block pt-5">
        The Page Speed Tracking feature allows you to monitor the load time of
        your website pages. It uses the event name <code>page_load_time</code>{' '}
        to help you analyze the performance of your website. To enable this
        feature, you need to toggle the option.
      </span>
      <span className="block pt-5">
        Tracking Load time will help you understand how your website performs
        and how individual pages compare to each other. This will help you
        identify slow pages and optimize them to improve the user experience.
      </span>
      <span className="block pt-5">
        After enabling the feature, head over to Google Analytics and create the
        "page_load_time" custom metric. You can find the instructions here:
        <a
          className="pt-5 font-medium text-blue-600 dark:text-blue-500 hover:underline"
          href="https://www.wpgoaltracker.com/q7lx"
        >
          Page Speed Tracking in Google Analytics
        </a>
      </span>
      <span className="block pt-5">
        <a
          className="pt-5 font-medium text-blue-600 dark:text-blue-500 hover:underline"
          target="_blank"
          href="https://www.wpgoaltracker.com/q7lx"
        >
          Click here to learn more about Page Speed tracking.
        </a>
      </span>
    </div>
  );
};

export default PageSpeedTrackingHelp;
