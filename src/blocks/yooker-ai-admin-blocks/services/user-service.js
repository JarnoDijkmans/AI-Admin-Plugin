import StatusCheck from "../error-handling/statushandler";

const getListAiSubscribers = async () => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/list-of-ai-subscribers/`,
      {
        method: "GET",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      }
    );

    const text = await response.text();
    const data = JSON.parse(text);

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

const getUserDetailsById = async (id) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/ai-subscriber-details/${id}`,
      {
        method: "GET",
        headers: {
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      }
    );

    const text = await response.text();

    const data = JSON.parse(text);

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });

    if (isSuccess) {
      const userData = data.data;

      return {
        ...userData,
        id: id,
      };
    } else {
      console.error("Failed status check");
      return null;
    }
  } catch (error) {
    console.error("Error fetching user data:", error);
    return null;
  }
};

const saveUser = async (user) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/post-ai-subscriber/`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": wpApiSettings.nonce,
        },
        body: JSON.stringify(user),
      }
    );

    const data = await response.json();

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });

    if (isSuccess) {
      return data;
    }
  } catch (error) {
    return data.data;
  }
};

const activateAccount = async (user) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/yooker-ai-activate-account/`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": wpApiSettings.nonce,
        },
        body: JSON.stringify(user),
      }
    );

    const data = await response.json();

    StatusCheck({
      status: data.status,
      message: data.message,
    });

    return data;
  } catch (error) {
    return error;
  }
};

const removeUser = async (userId) => {
  try {
    const response = await fetch(
      `${wpApiSettings.root}yooker-ai-admin/v1/delete-subscriber/${userId}`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": wpApiSettings.nonce,
        },
      }
    );

    const text = await response.text();
    const data = JSON.parse(text);
    console.log("data: ", data);

    const isSuccess = StatusCheck({
      status: data.status,
      message: data.message,
    });

    if (isSuccess) {
      return data.data;
    } else {
      return null;
    }
  } catch (error) {
    console.log("Error deleting user: ", error);
  }
};

export default {
  getListAiSubscribers,
  getUserDetailsById,
  saveUser,
  removeUser,
  activateAccount,
};
