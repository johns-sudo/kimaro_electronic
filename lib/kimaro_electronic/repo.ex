defmodule KimaroElectronic.Repo do
  use Ecto.Repo,
    otp_app: :kimaro_electronic,
    adapter: Ecto.Adapters.MyXQL
end
