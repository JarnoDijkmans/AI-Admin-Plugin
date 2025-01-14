import StatusCheck from "../error-handling/statushandler";

const getListSubscriptions = async () => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/subscriptions/`,
      {
        method: "GET",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      }
    );

    const data = await response.json();

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });

    if (isSuccess) {
      return data.data;
    }
  } catch (error) {
    console.error("Error fetching data:", error);
    return null;
  }
};

const getSubscriptionById = async (id) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/subscriptions/${id}`,
      {
        method: "GET",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      }
    );

    const data = await response.json();

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });
    if (isSuccess) {
      const subscriptionData = data.data;
      const parsedOptions = parseJSONField(subscriptionData.options);
      const parsedModel = parseJSONField(subscriptionData.model);
      return {
        ...subscriptionData,
        options: parsedOptions,
        model: parsedModel,
      };
    }
  } catch (error) {
    console.error("Error fetching subscription data:", error);
    return null;
  }
};

const saveSubscription = async (subscription) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/subscriptions/`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": wpApiSettings.nonce,
        },
        body: JSON.stringify(subscription),
      }
    );

    const data = await response.json();

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });
    if (isSuccess) {
      return { success: true, message: data.message };
    } else {
      return { success: false, message: data.message };
    }
  } catch (error) {
    console.error("Error saving subscription:", error);
    return {
      success: false,
      message: "An error occurred while saving the subscription.",
    };
  }
};

const parseJSONField = (field) => {
  if (typeof field === "string") {
    try {
      return JSON.parse(field);
    } catch (error) {
      console.error(`Failed to parse field: ${field}`, error);
      return [];
    }
  }
  return Array.isArray(field) ? field : [];
};

const removeSubscriptionForUser = async (subId, userId) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/admin-end-subscription/`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": wpApiSettings.nonce,
        },
        body: JSON.stringify({
          subscription_id: subId,
          user_id: userId,
        }),
      }
    );

    console.log("response: ", response);

    const data = await response.json();

    console.log("data: ", data);

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });

    if (isSuccess) {
      return data.data;
    }
  } catch (error) {
    console.error("Error creating new subscription: ", error);
    return null;
  }
};

// const removeSubscription = async (subscriberId) => {
//   try {
//     const response = await fetch(
//       "https://ai.yookerdesign.nl/wp-json/yooker-ai-admin/v1/delete-subscription/",
//       {
//         method: "DELETE",
//         headers: {
//           "Content-Type": "application/json",
//           "X-WP-Nonce": wpApiSettings.nonce,
//         },
//         body: JSON.stringify({
//           subscription_id: subscriberId,
//         }),
//       }
//     );

//     const text = await response.text();
//     const data = JSON.parse(text);

//     const isSuccess = StatusCheck({ status: data.data.status });

//     if (isSuccess) {
//       return data.data;
//     } else {
//       return null;
//     }
//   } catch (error) {
//     console.log("Error deleting user: ", error);
//   }
// };

export default {
  getListSubscriptions,
  getSubscriptionById,
  saveSubscription,
  removeSubscriptionForUser,
  // removeSubscription,
};
