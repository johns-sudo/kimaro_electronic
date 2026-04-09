defmodule KimaroElectronicWeb.PageController do
  use KimaroElectronicWeb, :controller

  def home(conn, _params) do
    render(conn, :home)
  end
end
