import EmailLinkTrackingHelp from './EmailLinkTrackingHelp';
import LinkTrackingHelp from './LinkTrackingHelp';
import PageSpeedTrackingHelp from './PageSpeedTrackingHelp';

const CustomEventsTrackingHelpSection = () => {
  return (
    <div>
      <LinkTrackingHelp />
      <EmailLinkTrackingHelp />
      <PageSpeedTrackingHelp />
    </div>
  );
};

export default CustomEventsTrackingHelpSection;
