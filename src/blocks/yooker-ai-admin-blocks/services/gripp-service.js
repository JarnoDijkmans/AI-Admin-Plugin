import StatusCheck from "../error-handling/statushandler";
const findIdByName = async (companyName) => {
  try {
    const response = await fetch(
      `${
        wpApiSettings.root
      }yooker-ai-admin/v1/get-gripp-client/?company=${encodeURIComponent(
        companyName
      )}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
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
    console.error("Error retrieving company ID", error);
  }
};

export default {
  findIdByName,
};
