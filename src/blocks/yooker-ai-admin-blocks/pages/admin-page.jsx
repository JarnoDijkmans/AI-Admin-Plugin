import AiGebruikers from "../components/ai-gebruikers";
import Subscriptions from "../components/subscriptions";
import Settings from "../components/settings";
import { TabPanel } from "@wordpress/components";

const AdminPage = () => {
  return (
    <TabPanel
      className="admin-page-panel"
      activeClass="active-tab"
      tabs={[
        {
          name: "gebruikers",
          title: "Gebruikers",
          className: "tab-gebruikers",
        },
        {
          name: "subscriptions",
          title: "Abonnementen",
          className: "tab-subscription",
        },
        {
          name: "gripp-settings",
          title: "Gripp Settings",
          className: "tab-settings",
        },
      ]}
    >
      {(tab) => (
        <div>
          {tab.name === "gebruikers" && (
            <div>
              <AiGebruikers />
            </div>
          )}
          {tab.name === "subscriptions" && (
            <div>
              <Subscriptions />
            </div>
          )}
          {tab.name === "gripp-settings" && (
            <div>
              <Settings />
            </div>
          )}
        </div>
      )}
    </TabPanel>
  );
};

export default AdminPage;
