import UserTrackingScreenshot from '../../assets/images/UserTrackingHelp.png';

const UserTrackingHelpSection = () => {
  return (
    <div>
      <div>
        <div className="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
          Goal Tracker and Google Tag Manager
        </div>
        <span className="block pt-5">
          <p>
            We are excited to introduce a new feature in Goal Tracker for Google
            Analytics that enables you to track users on your website using
            their User ID. This feature provides a more accurate and
            comprehensive understanding of user behavior, while ensuring that no
            sensitive information is sent to Google Analytics.
          </p>
          <p>
            The User ID tracking feature works by assigning a unique identifier
            to each user on your site, enabling you to analyze individual user
            interactions and behavior across multiple sessions and devices. This
            helps you gain valuable insights into your audience and improve your
            marketing strategies, user experience, and overall website
            performance.
          </p>
        </span>
        <img className="pt-5" src={UserTrackingScreenshot} />
        <span className="block pt-5 pb-5">
          Please note that the plugin strictly adheres to data privacy
          regulations and does not send any personally identifiable information
          (PII) or sensitive data to Google Analytics. The User ID is an
          anonymous identifier, ensuring that user privacy remains protected at
          all times.
        </span>
      </div>
    </div>
  );
};

export default UserTrackingHelpSection;
