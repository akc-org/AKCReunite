declare var wp: any;
declare var wpGoalTrackerGa: any;

import {
  createContext,
  useContext,
  useState,
  useEffect,
  ReactNode,
} from 'react';
import { TabContext } from 'ui/src/context/TabContext';

const { apiFetch } = wp;

interface GeneralSettingsContextValue {
  trackLinks: boolean;
  setTrackLinks: (value: boolean) => void;
  trackLinksType: string;
  setTrackLinksType: (value: string) => void;
  trackEmailLinks: boolean;
  setTrackEmailLinks: (value: boolean) => void;
  pageSpeedTracking: boolean;
  setPageSpeedTracking: (value: boolean) => void; // Add this line
  disableTrackingForAdmins: boolean;
  setDisableTrackingForAdmins: (value: boolean) => void;
  trackUsers: boolean;
  setTrackUsers: (value: boolean) => void;
  measurementID: string;
  setMeasurementID: (value: string) => void;
  gaDebug: boolean;
  setGaDebug: (value: boolean) => void;
  disablePageView: boolean;
  setDisablePageView: (value: boolean) => void;
  noSnippet: boolean;
  setNoSnippet: (value: boolean) => void;
  multiTrackers: boolean;
  setMultiTrackers: (value: boolean) => void;
  permittedRoles: Role[];
  setPermittedRoles: (value: Role[]) => void;
  showTutorial: boolean;
  setShowTutorial: (value: boolean) => void;
  setSettings: (event: React.FormEvent) => Promise<void>;
  getSettings: () => Promise<void>;
  updateSettings: (data: any) => void;
  updatePermittedRoles: (data: any) => void;
}

interface Role {
  id: string;
  name: string;
  // ... other properties if necessary
}
const GeneralSettingsContext = createContext<
  GeneralSettingsContextValue | undefined
>(undefined);

export const useGeneralSettings = (): GeneralSettingsContextValue => {
  const context = useContext(GeneralSettingsContext);

  if (!context) {
    throw new Error(
      'useGeneralSettings must be used within a GeneralSettingsProvider',
    );
  }

  return context;
};

interface GeneralSettingsProviderProps {
  children: ReactNode;
}

interface Role {
  id: string;
  name: string;
}

interface Data {
  measurementID?: string;
  trackLinks?: {
    enabled?: boolean;
    type?: string;
  };
  trackEmailLinks?: boolean;
  pageSpeedTracking?: boolean;
  disableTrackingForAdmins?: boolean;
  trackUsers?: boolean;
  gaDebug?: boolean;
  disablePageView?: boolean;
  noSnippet?: boolean;
  multiTrackers?: boolean;
  permittedRoles?: Role[];
  hideGeneralSettingsTutorial?: boolean;
}

export const GeneralSettingsProvider = ({
  children,
}: GeneralSettingsProviderProps): JSX.Element => {
  const [trackLinks, setTrackLinks] = useState(false);
  const [trackLinksType, setTrackLinksType] = useState('');
  const [trackEmailLinks, setTrackEmailLinks] = useState(false);
  const [pageSpeedTracking, setPageSpeedTracking] = useState(false);
  const [disableTrackingForAdmins, setDisableTrackingForAdmins] = useState(
    false,
  );
  const [trackUsers, setTrackUsers] = useState(false);
  const [measurementID, setMeasurementID] = useState('');
  const [gaDebug, setGaDebug] = useState(false);
  const [disablePageView, setDisablePageView] = useState(false);
  const [noSnippet, setNoSnippet] = useState(false);
  const [multiTrackers, setMultiTrackers] = useState(false);
  const [permittedRoles, setPermittedRoles] = useState<Role[]>([]);
  const [showTutorial, setShowTutorial] = useState(false);

  const tabs = useContext(TabContext);
  if (!tabs) {
    throw new Error('ChildComponent must be used within a TabContextProvider');
  }

  const { updateHasIssueByName } = tabs;

  useEffect(() => {
    const fetchSettings = async () => {
      await getSettings();
    };
    fetchSettings();
  }, []);

  const updateSettings = (data: Data) => {
    if (data) {
      setMeasurementID(data.measurementID);

      setTrackLinks(data.trackLinks.enabled);
      if (data.trackLinks && data.trackLinks.enabled) {
        if (data.trackLinks.type) setTrackLinksType(data.trackLinks.type);
      }

      setTrackEmailLinks(data.trackEmailLinks);
      setPageSpeedTracking(data.pageSpeedTracking);
      setDisableTrackingForAdmins(data.disableTrackingForAdmins);
      setTrackUsers(data.trackUsers);
      setGaDebug(data.gaDebug);
      setDisablePageView(data.disablePageView);
      setNoSnippet(data.noSnippet);
      setMultiTrackers(data.multiTrackers);

      if (data.permittedRoles) {
        let test: Role[] = [];
        data.permittedRoles.forEach(role => {
          if (wpGoalTrackerGa) {
            test.push(
              wpGoalTrackerGa.wp_roles.find(({ id }: Role) => id == role.id),
            );
          }
        });
        setPermittedRoles(test);
      }

      if (!data.hideGeneralSettingsTutorial) {
        setShowTutorial(!data.hideGeneralSettingsTutorial);
      }
    }

    // Do we need to show a badge
    if (data.measurementID === '' && data.noSnippet === false) {
      updateHasIssueByName('Settings', true);
    }
  };

  const getSettings = async () => {
    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/get_general_settings',
    });
    if (data) {
      updateSettings(data);
      // setNeedSave(true);
    } else {
      updateSettings({});
    }
    return data;
  };

  const setSettings = async (event: any) => {
    event.preventDefault();
    // setIsSaving(true);
    // setNeedSave(false);
    const generalSettings = {
      measurementID: measurementID,
      gaDebug: gaDebug,
      disablePageView: disablePageView,
      noSnippet: noSnippet,
      multiTrackers: multiTrackers,
      trackLinks: {
        enabled: trackLinks,
        type: trackLinksType ? trackLinksType : 'all',
      },
      trackEmailLinks: trackEmailLinks,
      pageSpeedTracking: pageSpeedTracking,
      disableTrackingForAdmins: disableTrackingForAdmins,
      trackUsers: trackUsers,
      permittedRoles: permittedRoles,
    };

    let data = await apiFetch({
      path:
        wpGoalTrackerGa.rest.namespace +
        wpGoalTrackerGa.rest.version +
        '/set_general_settings',
      method: 'POST',
      data: { generalSettings: generalSettings },
    });

    // if (isEqual(generalSettings, data)) {
    //   setError(false);
    //   setIsSaving(false);
    //   setNeedSave(false);
    // } else {
    //   setIsSaving(false);
    //   setError(true);
    //   setNeedSave(true);
    //   updateSettings(data);
    // }
    // setNotice(true);
  };

  function updatePermittedRoles(roles: []) {
    setPermittedRoles(roles);
  }

  return (
    <GeneralSettingsContext.Provider
      value={{
        trackLinks,
        setTrackLinks,
        trackLinksType,
        setTrackLinksType,
        trackEmailLinks,
        setTrackEmailLinks,
        pageSpeedTracking,  
        setPageSpeedTracking, // Add this line
        disableTrackingForAdmins,
        setDisableTrackingForAdmins,
        trackUsers,
        setTrackUsers,
        measurementID,
        setMeasurementID,
        gaDebug,
        setGaDebug,
        disablePageView,
        setDisablePageView,
        noSnippet,
        setNoSnippet,
        multiTrackers,
        setMultiTrackers,
        permittedRoles,
        setPermittedRoles,
        pageSpeedTracking, // Add this line
        setPageSpeedTracking, // Add this line
        showTutorial,
        setShowTutorial,
        setSettings,
        getSettings,
        updateSettings,
        updatePermittedRoles,
      }}
    >
      {children}
    </GeneralSettingsContext.Provider>
  );
};
